<?php
declare(strict_types=1);

namespace App\Controller\Master;

use App\Controller\AppController;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Doctors Controller
 *
 * @property \App\Model\Table\DoctorsTable $Doctors
 * @method \App\Model\Entity\Doctor[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DoctorsController extends AppController
{
    public function searchModal()
    {
        $this->request->allowMethod(['get']);
        $this->autoRender = false;

        $keyword = trim((string)$this->request->getQuery('q'));

        $query = $this->Doctors->find()
            ->select(['id', 'code', 'doctor_name', 'specialist', 'polyclinic_name'])
            ->order(['doctor_name' => 'ASC'])
            ->limit(50);

        if ($keyword !== '') {
            $query->where([
                'OR' => [
                    'doctor_name LIKE' => '%' . $keyword . '%',
                    'code LIKE' => '%' . $keyword . '%',
                ],
            ]);
        }

        $this->response = $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($query->toArray()));

        return $this->response;
    }
    public function index()
    {
        $key = $this->request->getQuery('key');
        $gender = $this->request->getQuery('gender');
        $isActive = $this->request->getQuery('is_active');

        $query = $this->Doctors->find();

        if (!empty($key)) {
            $query->where([
                'OR' => [
                    'Doctors.code LIKE' => '%' . $key . '%',
                    'Doctors.doctor_name LIKE' => '%' . $key . '%',
                    'Doctors.nik LIKE' => '%' . $key . '%',
                    'Doctors.specialist LIKE' => '%' . $key . '%',
                    'Doctors.polyclinic_name LIKE' => '%' . $key . '%',
                    'Doctors.phone_number LIKE' => '%' . $key . '%',
                ],
            ]);
        }

        if (!empty($gender)) {
            $query->where(['Doctors.gender' => $gender]);
        }

        if ($isActive !== null && $isActive !== '') {
            $query->where(['Doctors.is_active' => $isActive]);
        }

        $this->paginate = [
            'order' => ['Doctors.id' => 'DESC'],
        ];

        $doctors = $this->paginate($query);

        // Daftar poli dan spesialis untuk filter popup export
        $polyOptions = [];
        $polyRows = $this->Doctors->find()
            ->select(['polyclinic_name'])
            ->distinct(['polyclinic_name'])
            ->where(['Doctors.polyclinic_name IS NOT' => null, 'Doctors.polyclinic_name !=' => ''])
            ->orderBy(['Doctors.polyclinic_name' => 'ASC'])
            ->enableHydration(false)
            ->all();
        foreach ($polyRows as $r) {
            $polyOptions[$r['polyclinic_name']] = $r['polyclinic_name'];
        }

        $specialistOptions = [];
        $specRows = $this->Doctors->find()
            ->select(['specialist'])
            ->distinct(['specialist'])
            ->where(['Doctors.specialist IS NOT' => null, 'Doctors.specialist !=' => ''])
            ->orderBy(['Doctors.specialist' => 'ASC'])
            ->enableHydration(false)
            ->all();
        foreach ($specRows as $r) {
            $specialistOptions[$r['specialist']] = $r['specialist'];
        }

        $this->set(compact('doctors', 'key', 'gender', 'isActive', 'polyOptions', 'specialistOptions'));
    }

        public function excel()
    {
        $this->request->allowMethod(['get']);

        $key        = trim((string)$this->request->getQuery('key'));
        $gender     = trim((string)$this->request->getQuery('gender'));
        $isActive   = trim((string)$this->request->getQuery('is_active'));
        $polys      = array_values(array_filter((array)$this->request->getQuery('polyclinic')));
        $specialist = array_values(array_filter((array)$this->request->getQuery('specialist')));

        $query = $this->Doctors->find();

        if (!empty($polys)) {
            $query->where(['Doctors.polyclinic_name IN' => $polys]);
        }
        if (!empty($specialist)) {
            $query->where(['Doctors.specialist IN' => $specialist]);
        }
        if ($gender !== '') {
            $query->where(['Doctors.gender' => $gender]);
        }
        if ($isActive !== '') {
            $query->where(['Doctors.is_active' => $isActive]);
        }
        if ($key !== '') {
            $query->where([
                'OR' => [
                    'Doctors.code LIKE' => '%' . $key . '%',
                    'Doctors.doctor_name LIKE' => '%' . $key . '%',
                    'Doctors.nik LIKE' => '%' . $key . '%',
                    'Doctors.specialist LIKE' => '%' . $key . '%',
                    'Doctors.polyclinic_name LIKE' => '%' . $key . '%',
                    'Doctors.phone_number LIKE' => '%' . $key . '%',
                ],
            ]);
        }

        $query->orderBy(['Doctors.polyclinic_name' => 'ASC', 'Doctors.doctor_name' => 'ASC']);

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

        $genderLabels = ['male' => 'Laki-laki', 'female' => 'Perempuan'];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Dokter');

        $headers = [
            'No', 'Kode', 'Nama Dokter', 'NIK', 'Spesialis', 'Poliklinik', 'Jenis Kelamin',
            'No SIP', 'No STR', 'No Telepon', 'Status', 'Tgl Dibuat',
        ];
        $lastCol = 'L';

        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2C3E50']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $row = 2;
        $no = 1;
        foreach ($query as $d) {
            $isAktif = ($d->is_active === 'Y' || $d->is_active === 1 || $d->is_active === '1');
            $g = strtolower((string)($d->gender ?? ''));

            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValueExplicit("B{$row}", (string)($d->code ?? ''), DataType::TYPE_STRING);
            $sheet->setCellValue("C{$row}", (string)($d->doctor_name ?? ''));
            $sheet->setCellValueExplicit("D{$row}", (string)($d->nik ?? ''), DataType::TYPE_STRING);
            $sheet->setCellValue("E{$row}", (string)($d->specialist ?? ''));
            $sheet->setCellValue("F{$row}", (string)($d->polyclinic_name ?? ''));
            $sheet->setCellValue("G{$row}", $genderLabels[$g] ?? (string)($d->gender ?? '-'));
            $sheet->setCellValueExplicit("H{$row}", (string)($d->sip_number ?? ''), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("I{$row}", (string)($d->str_number ?? ''), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("J{$row}", (string)($d->phone_number ?? ''), DataType::TYPE_STRING);
            $sheet->setCellValue("K{$row}", $isAktif ? 'Aktif' : 'Non Aktif');
            $sheet->setCellValue("L{$row}", $fmtDate($d->created ?? null, 'd-m-Y H:i'));
            $row++;
        }

        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->freezePane('A2');

        $tmp = tempnam(sys_get_temp_dir(), 'doc_');
        (new Xlsx($spreadsheet))->save($tmp);
        $body = file_get_contents($tmp);
        unlink($tmp);

        return $this->response
            ->withType('xlsx')
            ->withDownload('dokter_' . date('Ymd_His') . '.xlsx')
            ->withStringBody($body);
    }

    public function view($id = null)
    {
        $doctor = $this->Doctors->get($id, [
            'contain' => ['DoctorSchedules'],
        ]);

        $this->set(compact('doctor'));
    }

    public function add()
    {
        $doctor = $this->Doctors->newEmptyEntity();

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            unset($data['polyclinic_id']);

            // Kode SELALU otomatis, input manual dari form diabaikan
            $data['code'] = $this->generateDoctorCode();

            $data['is_create'] = $data['is_create'] ?? 1;
            $data['is_update'] = $data['is_update'] ?? 0;
            $data['is_delete'] = $data['is_delete'] ?? 0;
            $data['is_active'] = $data['is_active'] ?? 'Y';

            $doctor = $this->Doctors->patchEntity($doctor, $data);

            if ($this->Doctors->save($doctor)) {
                $this->Flash->success(__('The doctor has been saved.'));

                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error(__('The doctor could not be saved. Please, try again.'));
        }

        // polyclinics, religions, specialists diambil di view lewat $this->Krs->...()
        $this->set(compact('doctor'));
    }

    public function edit($id = null)
    {
        $doctor = $this->Doctors->get($id, [
            'contain' => [],
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();

            unset($data['polyclinic_id']);

            // Kode tidak diubah saat edit, tetap pakai kode lama
            unset($data['code']);

            $data['is_update'] = 1;

            $doctor = $this->Doctors->patchEntity($doctor, $data);

            if ($this->Doctors->save($doctor)) {
                $this->Flash->success(__('The doctor has been saved.'));

                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error(__('The doctor could not be saved. Please, try again.'));
        }

        $this->set(compact('doctor'));
    }

    public function searchByPolyclinic()
    {
        $this->request->allowMethod(['get']);
        $this->autoRender = false;

        $polyName = trim((string)$this->request->getQuery('poly_name'));
        $q = trim((string)$this->request->getQuery('q'));

        $query = $this->Doctors->find()
            ->select([
                'id',
                'doctor_name',
                'specialist',
                'polyclinic_name',
            ])
            ->where([
                'Doctors.is_active' => 'Y',
            ])
            ->order([
                'Doctors.doctor_name' => 'ASC',
            ])
            ->limit(20);

        if ($polyName !== '') {
            $query->where(function ($exp) use ($polyName) {
                return $exp->eq('LOWER(Doctors.polyclinic_name)', mb_strtolower($polyName));
            });
        }

        if ($q !== '') {
            $query->where([
                'Doctors.doctor_name LIKE' => '%' . $q . '%',
            ]);
        }

        $data = [];

        foreach ($query->all() as $doctor) {
            $data[] = [
                'id' => $doctor->id,
                'doctor_name' => $doctor->doctor_name,
                'specialist' => $doctor->specialist,
                'polyclinic_name' => $doctor->polyclinic_name,
            ];
        }

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($data));
    }

    /**
     * Generate kode dokter otomatis berdasarkan id terakhir.
     * Format: DR-0001, DR-0002, dst.
     */
    private function generateDoctorCode(): string
    {
        $lastDoctor = $this->Doctors->find()
            ->select(['id'])
            ->order(['Doctors.id' => 'DESC'])
            ->first();

        $nextId = $lastDoctor ? ((int)$lastDoctor->id + 1) : 1;

        return 'DR-' . str_pad((string)$nextId, 4, '0', STR_PAD_LEFT);
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        $doctor = $this->Doctors->get($id);

        if ($this->Doctors->delete($doctor)) {
            $this->Flash->success(__('The doctor has been deleted.'));
        } else {
            $this->Flash->error(__('The doctor could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

     public function listActive()
    {
        $this->request->allowMethod(['get']);
        $this->autoRender = false;

        $doctors = $this->Doctors->find()
            ->select(['id', 'doctor_name', 'specialist', 'polyclinic_name'])
            ->where(['is_active' => 'Y'])
            ->order(['doctor_name' => 'ASC'])
            ->enableHydration(false)
            ->toArray();

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($doctors));
    }
}