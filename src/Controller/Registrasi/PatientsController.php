<?php
declare(strict_types=1);

namespace App\Controller\Registrasi;

use App\Controller\AppController;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Patients Controller
 *
 * @property \App\Model\Table\PatientsTable $Patients
 */
class PatientsController extends AppController
{
    /**
     * INDEX — dengan search & filter
     */
    public function index()
    {
        $query = $this->Patients->find()
            ->where([
                'OR' => [
                    ['Patients.is_delete !=' => 1],
                    ['Patients.is_delete IS' => null],
                ],
            ])
            ->orderBy(['Patients.id' => 'ASC']);

        // Filter pencarian
        $key = $this->request->getQuery('key');
        if (!empty($key)) {
            $query->where([
                'OR' => [
                    'Patients.code_hash'             => \App\Utility\DataCipher::hash((string)$key),
                    'Patients.old_medical_record_no LIKE' => '%' . $key . '%',
                    'Patients.full_name LIKE'        => '%' . $key . '%',
                    'Patients.identity_number LIKE'  => '%' . $key . '%',
                    'Patients.phone_number LIKE'     => '%' . $key . '%',
                ],
            ]);
        }

        // Filter gender
        $gender = $this->request->getQuery('gender');
        if (!empty($gender)) {
            $query->where(['Patients.gender' => $gender]);
        }

        // Filter is_active
        $isActive = $this->request->getQuery('is_active');
        if ($isActive !== null && $isActive !== '') {
            $query->where(['Patients.is_active' => $isActive]);
        }

        $numberingQuery = (clone $query)
            ->select(['Patients.id'])
            ->orderBy(['Patients.id' => 'ASC'])
            ->enableHydration(false);

        $idOrderMap = [];
        $rank = 1;
        foreach ($numberingQuery->all() as $row) {
            $idOrderMap[$row['id']] = $rank++;
        }

        $this->paginate = ['limit' => 15];
        $patients = $this->paginate($query);

        // Daftar kota/kabupaten untuk filter popup export
        $cityRows = $this->Patients->find()
            ->select(['city'])
            ->distinct(['city'])
            ->where([
                'Patients.city IS NOT' => null,
                'Patients.city !=' => '',
                'OR' => [
                    ['Patients.is_delete !=' => 1],
                    ['Patients.is_delete IS' => null],
                ],
            ])
            ->orderBy(['Patients.city' => 'ASC'])
            ->enableHydration(false)
            ->all();

        $cityOptions = [];
        foreach ($cityRows as $row) {
            $cityOptions[$row['city']] = $row['city'];
        }

        $this->set(compact('patients', 'key', 'gender', 'isActive', 'idOrderMap', 'cityOptions'));
    }

        /**
     * EXCEL — export data pasien dengan filter dari popup
     */
    public function excel()
    {
        $this->request->allowMethod(['get']);

        $key       = trim((string)$this->request->getQuery('key'));
        $gender    = trim((string)$this->request->getQuery('gender'));
        $isActive  = trim((string)$this->request->getQuery('is_active'));
        $blood     = trim((string)$this->request->getQuery('blood_type'));
        $cities    = array_values(array_filter((array)$this->request->getQuery('city')));
        $dateFrom  = (string)$this->request->getQuery('date_from');
        $dateTo    = (string)$this->request->getQuery('date_to');

        $validDate = static function (string $d): ?string {
            $dt = \DateTimeImmutable::createFromFormat('!Y-m-d', $d);
            return ($dt && $dt->format('Y-m-d') === $d) ? $d : null;
        };
        $dateFrom = $validDate($dateFrom);
        $dateTo   = $validDate($dateTo);

        if ($dateFrom && $dateTo && $dateFrom > $dateTo) {
            [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
        }

        $query = $this->Patients->find()
            ->where([
                'OR' => [
                    ['Patients.is_delete !=' => 1],
                    ['Patients.is_delete IS' => null],
                ],
            ]);

        // Tanggal daftar bersifat opsional (kosong = semua pasien)
        if ($dateFrom) {
            $query->where(['DATE(Patients.created) >=' => $dateFrom]);
        }
        if ($dateTo) {
            $query->where(['DATE(Patients.created) <=' => $dateTo]);
        }
        if ($gender !== '') {
            $query->where(['Patients.gender' => $gender]);
        }
        if ($isActive !== '') {
            $query->where(['Patients.is_active' => $isActive]);
        }
        if ($blood !== '') {
            $query->where(['Patients.blood_type' => $blood]);
        }
        if (!empty($cities)) {
            $query->where(['Patients.city IN' => $cities]);
        }
        if ($key !== '') {
            $query->where([
                'OR' => [
                    'Patients.code_hash' => \App\Utility\DataCipher::hash($key),
                    'Patients.old_medical_record_no LIKE' => '%' . $key . '%',
                    'Patients.full_name LIKE' => '%' . $key . '%',
                    'Patients.identity_number LIKE' => '%' . $key . '%',
                    'Patients.phone_number LIKE' => '%' . $key . '%',
                ],
            ]);
        }

        $query->orderBy(['Patients.id' => 'ASC']);

        $fmtDate = static function ($v, string $f = 'd-m-Y'): string {
            if (empty($v)) {
                return '-';
            }
            if (is_object($v) && method_exists($v, 'format')) {
                return $v->format($f);
            }
            $t = strtotime((string)$v);
            return $t ? date($f, $t) : '-';
        };

        $calcAge = static function ($v): string {
            if (empty($v)) {
                return '-';
            }
            try {
                $birth = is_object($v) && method_exists($v, 'format')
                    ? new \DateTime($v->format('Y-m-d'))
                    : new \DateTime((string)$v);
                return (string)(new \DateTime())->diff($birth)->y;
            } catch (\Throwable $e) {
                return '-';
            }
        };

        $genderLabels = ['male' => 'Laki-laki', 'female' => 'Perempuan'];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Pasien');

        $headers = [
            'No', 'No Rekam Medis', 'No RM Lama', 'Gelar', 'Nama Lengkap', 'Jenis Identitas',
            'No Identitas', 'Jenis Kelamin', 'Gol Darah', 'Alergi', 'Tgl Lahir', 'Umur (th)',
            'Alamat', 'Nama Ayah', 'Nama Ibu', 'No Telepon', 'No Kartu Asuransi',
            'Kewarganegaraan', 'Negara', 'Provinsi', 'Kota/Kab', 'Kecamatan', 'Kelurahan',
            'Pekerjaan', 'Agama', 'Pendidikan', 'Suku Bangsa', 'Status Nikah', 'Status', 'Tgl Daftar',
        ];
        $lastCol = 'AD';

        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2C3E50']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $row = 2;
        $no = 1;
        foreach ($query as $p) {
            $isAktif = ($p->is_active === 'Y' || $p->is_active === 1 || $p->is_active === '1');
            $g = strtolower((string)($p->gender ?? ''));

            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValueExplicit("B{$row}", (string)($p->code ?? ''), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("C{$row}", (string)($p->old_medical_record_no ?? ''), DataType::TYPE_STRING);
            $sheet->setCellValue("D{$row}", (string)($p->title ?? ''));
            $sheet->setCellValue("E{$row}", (string)($p->full_name ?? ''));
            $sheet->setCellValue("F{$row}", (string)($p->identity_type ?? ''));
            $sheet->setCellValueExplicit("G{$row}", (string)($p->identity_number ?? ''), DataType::TYPE_STRING);
            $sheet->setCellValue("H{$row}", $genderLabels[$g] ?? (string)($p->gender ?? '-'));
            $sheet->setCellValue("I{$row}", (string)($p->blood_type ?? ''));
            $sheet->setCellValue("J{$row}", (string)($p->allergy ?? ''));
            $sheet->setCellValue("K{$row}", $fmtDate($p->birth_date ?? null));
            $sheet->setCellValue("L{$row}", $calcAge($p->birth_date ?? null));
            $sheet->setCellValue("M{$row}", (string)($p->address ?? ''));
            $sheet->setCellValue("N{$row}", (string)($p->father_name ?? ''));
            $sheet->setCellValue("O{$row}", (string)($p->mother_name ?? ''));
            $sheet->setCellValueExplicit("P{$row}", (string)($p->phone_number ?? ''), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("Q{$row}", (string)($p->insurance_card_no ?? ''), DataType::TYPE_STRING);
            $sheet->setCellValue("R{$row}", (string)($p->citizenship ?? ''));
            $sheet->setCellValue("S{$row}", (string)($p->nationality ?? ''));
            $sheet->setCellValue("T{$row}", (string)($p->province ?? ''));
            $sheet->setCellValue("U{$row}", (string)($p->city ?? ''));
            $sheet->setCellValue("V{$row}", (string)($p->district ?? ''));
            $sheet->setCellValue("W{$row}", (string)($p->subdistrict ?? ''));
            $sheet->setCellValue("X{$row}", (string)($p->occupation ?? ''));
            $sheet->setCellValue("Y{$row}", (string)($p->religion ?? ''));
            $sheet->setCellValue("Z{$row}", (string)($p->education ?? ''));
            $sheet->setCellValue("AA{$row}", (string)($p->ethnic_group ?? ''));
            $sheet->setCellValue("AB{$row}", (string)($p->marital_status ?? ''));
            $sheet->setCellValue("AC{$row}", $isAktif ? 'Aktif' : 'Non Aktif');
            $sheet->setCellValue("AD{$row}", $fmtDate($p->created ?? null, 'd-m-Y H:i'));
            $row++;
        }

        foreach (range('A', 'Z') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        foreach (['AA', 'AB', 'AC', 'AD'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->freezePane('A2');

        $tmp = tempnam(sys_get_temp_dir(), 'pat_');
        (new Xlsx($spreadsheet))->save($tmp);
        $body = file_get_contents($tmp);
        unlink($tmp);

        return $this->response
            ->withType('xlsx')
            ->withDownload('pasien_' . date('Ymd_His') . '.xlsx')
            ->withStringBody($body);
    }

    /**
     * VIEW
     */
public function view($id = null)
{
    $patient = $this->Patients->get($id, contain: ['Registrations.Doctors']);

    $this->set(compact('patient'));
}
    /** Dropdown wilayah bertingkat (data dari config/wilayah.php lewat App\Utility\Wilayah). */
    private function jsonWilayah(array $data)
    {
        $this->autoRender = false;

        return $this->response->withType('application/json')->withStringBody(json_encode($data));
    }

    public function getKabupatens()
    {
        $this->request->allowMethod(['get']);

        return $this->jsonWilayah(\App\Utility\Wilayah::kabupaten((string)$this->request->getQuery('id_prov')));
    }

    public function getKecamatans()
    {
        $this->request->allowMethod(['get']);

        return $this->jsonWilayah(\App\Utility\Wilayah::kecamatan((string)$this->request->getQuery('id_kab')));
    }

    /** Kelurahan diisi teks bebas, tidak ada data master. */
    public function getKelurahans()
    {
        $this->request->allowMethod(['get']);

        return $this->jsonWilayah([]);
    }

    /**
     * ADD
     */
    public function add()
    {
        $patient = $this->Patients->newEmptyEntity();

        $previewCode = '';
        $patient->code = $previewCode;

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            $data['code'] = $this->Patients->generatePatientCode($data['birth_date'] ?? '');

            if (empty($data['is_active'])) {
                $data['is_active'] = 'Y';
            }

            $patient = $this->Patients->patchEntity($patient, $data);

            if ($this->Patients->save($patient)) {
                $this->Flash->success(__('Data pasien berhasil disimpan.'));
                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error(__('Data pasien gagal disimpan. Periksa kembali isian form.'));

            if (empty($patient->code)) {
                $patient->code = $previewCode;
            }
        }

        $this->set(compact('patient', 'previewCode'));
    }

    /**
     * EDIT
     */
    public function edit($id = null)
    {
        $patient = $this->Patients->get($id, contain: []);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();

            if (!empty($patient->code)) {
                $data['code'] = $patient->code;
            } else {
                $data['code'] = $this->Patients->generatePatientCode($data['birth_date'] ?? $patient->birth_date ?? '');
            }

            // No. RM lama tidak boleh diubah dari form edit.
            unset($data['old_medical_record_no']);

            $patient = $this->Patients->patchEntity($patient, $data);

            if ($this->Patients->save($patient)) {
                $this->Flash->success(__('Data pasien berhasil diupdate.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Data pasien gagal diupdate.'));
        }

        $this->set(compact('patient'));
    }

    /**
     * DELETE — soft delete
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        $patient = $this->Patients->get($id);
        $patient->is_delete = 1;

        if ($this->Patients->save($patient)) {
            $this->Flash->success(__('Data pasien berhasil dihapus.'));
        } else {
            $this->Flash->error(__('Data pasien gagal dihapus.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}