<?php
declare(strict_types=1);

namespace App\Controller\Penunjang;

use App\Controller\AppController;
use Cake\I18n\FrozenTime;

/**
 * Simpan di: src/Controller/Penunjang/RadiologyController.php
 *
 * ASUMSI (sesuaikan bila beda):
 *  - Orders belongsTo Patients, Doctors, Registrations; punya asosiasi OrderRadiologies
 *  - OrderRadiologies: order_id, code, examination_name, tariff, qty, total, approval_note
 *  - Patients: code, full_name, birth_date, gender|sex ; Registrations: registration_no|registration_number, service_type
 *  - MasterRadiologies & PharmacyItems: nama kolom dideteksi otomatis lewat pick()
 */
class RadiologyController extends AppController
{
    // =====================================================================
    // INDEX
    // =====================================================================
    public function index()
    {
        $Exams = $this->fetchTable('RadiologyExaminations');

        $by = (string)$this->request->getQuery('by', 'patient_name');
        $q = trim((string)$this->request->getQuery('q', ''));
        $dateFrom = (string)$this->request->getQuery('date_from', '');

        if (!in_array($by, ['patient_name', 'medical_record_no', 'rad_no'], true)) {
            $by = 'patient_name';
        }

        $query = $Exams->find()->order(['RadiologyExaminations.received_at' => 'DESC']);

        if ($q !== '') {
            $query->where(["RadiologyExaminations.$by LIKE" => '%' . $q . '%']);
        }
        if ($dateFrom !== '') {
            $query->where(['RadiologyExaminations.received_at >=' => $dateFrom . ' 00:00:00']);
        }

        $examinations = $this->paginate($query, ['limit' => 20]);

        $Orders = $this->fetchTable('Orders');
        $Items = $this->fetchTable('OrderRadiologies');

        $processedIds = $Exams->find()->select(['order_id'])->where(['order_id IS NOT' => null]);

        $orders = $Orders->find()
            ->contain(['Patients', 'Doctors', 'Registrations'])
            ->innerJoinWith('OrderRadiologies')
            ->where(['Orders.id NOT IN' => $processedIds])
            ->group(['Orders.id'])
            ->order(['Orders.tgl_order' => 'DESC'])
            ->limit(100)
            ->all();

        $orderIds = $orders->extract('id')->toList();
        $namesByOrder = [];
        if ($orderIds) {
            foreach ($Items->find()->where(['order_id IN' => $orderIds])->all() as $it) {
                $namesByOrder[$it->order_id][] = $it->examination_name;
            }
        }

        $requests = [];
        foreach ($orders as $o) {
            $requests[] = [
                'id' => $o->id,
                'no_order' => $o->no_order,
                'rm' => $o->patient->code ?? '-',
                'tgl_order' => $o->tgl_order,
                'service_type' => $o->registration->service_type ?? null,
                'patient_name' => $o->patient->full_name ?? '-',
                'rad_names' => implode(', ', $namesByOrder[$o->id] ?? []),
                'doctor_name' => $o->doctor->doctor_name ?? ($o->doctor->name ?? '-'),
                'pesan' => $o->pesan,
            ];
        }

        $officer = $this->officerName();

        $this->set(compact('examinations', 'requests', 'by', 'q', 'dateFrom', 'officer'));
    }

    // =====================================================================
    // PROSES  (order masuk -> pemeriksaan), lalu langsung ke halaman Tindakan
    // =====================================================================
    public function proses($orderId = null)
    {
        $this->request->allowMethod(['post']);

        $Exams = $this->fetchTable('RadiologyExaminations');
        $ExamItems = $this->fetchTable('RadiologyExaminationItems');
        $Orders = $this->fetchTable('Orders');
        $OrderItems = $this->fetchTable('OrderRadiologies');

        $order = $Orders->get($orderId, contain: ['Patients', 'Doctors', 'Registrations']);

        $existing = $Exams->find()->where(['order_id' => $order->id])->first();
        if ($existing) {
            $this->Flash->error('Order ini sudah diproses.');
            return $this->redirect(['action' => 'tindakan', $existing->id]);
        }

        $items = $OrderItems->find()->where(['order_id' => $order->id])->all();
        if ($items->isEmpty()) {
            $this->Flash->error('Order tidak memiliki tindakan radiologi.');
            return $this->redirect(['action' => 'index']);
        }

        $officer = $this->officerName();
        $types = ['rawat_jalan' => 'Rawat Jalan', 'rawat_inap' => 'Rawat Inap', 'igd' => 'IGD'];
        $reg = $order->registration;
        $p = $order->patient;
        $newId = null;

        $ok = $Exams->getConnection()->transactional(function () use ($Exams, $ExamItems, $order, $items, $officer, $types, $reg, $p, &$newId) {
            $exam = $Exams->newEntity([
                'rad_no' => $this->generateRadNo($Exams),
                'patient_origin' => 'rs',
                'order_id' => $order->id,
                'no_order' => $order->no_order,
                'registration_id' => $order->registration_id,
                'registration_no' => $reg->registration_no ?? ($reg->registration_number ?? null),
                'patient_id' => $order->patient_id,
                'medical_record_no' => $p->code ?? null,
                'patient_name' => $p->full_name ?? '-',
                'birth_date' => $this->dateOf($p->birth_date ?? null),
                'gender' => $this->genderOf($p),
                'doctor_id' => $order->doctor_id,
                'doctor_name' => $order->doctor->doctor_name ?? ($order->doctor->name ?? null),
                'sender_doctor_id' => $order->doctor_id,
                'patient_type' => $types[$reg->service_type ?? ''] ?? null,
                'doctor_message' => $order->pesan,
                'eorder_note' => $order->pesan,
                'status' => 'received',
                'received_at' => FrozenTime::now(),
                'processed_by' => $officer,
                'officer_name' => $officer,
            ]);

            if (!$Exams->save($exam)) {
                return false;
            }
            $newId = $exam->id;

            foreach ($items as $it) {
                $row = $ExamItems->newEntity([
                    'examination_id' => $exam->id,
                    'order_item_id' => $it->id,
                    'code' => $it->code,
                    'examination_name' => $it->examination_name,
                    'tariff' => $it->tariff,
                    'qty' => $it->qty ?? 1,
                    'total' => $it->total ?? ($it->tariff * ($it->qty ?? 1)),
                    'approval_note' => $it->approval_note,
                    'status' => 'received',
                ]);
                if (!$ExamItems->save($row)) {
                    return false;
                }
            }

            return true;
        });

        if (!$ok) {
            $this->Flash->error('Gagal memproses permintaan.');
            return $this->redirect(['action' => 'index']);
        }

        $this->Flash->success('Permintaan radiologi diproses. Lengkapi data tindakan.');
        return $this->redirect(['action' => 'tindakan', $newId]);
    }

    // =====================================================================
    // TINDAKAN UNIT RADIOLOGI
    // =====================================================================
    public function tindakan($id = null)
    {
        $exam = null;
        if ($id !== null) {
            $exam = $this->fetchTable('RadiologyExaminations')->get($id, contain: [
                'RadiologyExaminationItems',
                'RadiologyExaminationSupplies',
            ]);
        }

        $doctors = $this->doctorOptions();
        $officer = $this->officerName();

        $this->set(compact('exam', 'doctors', 'officer'));
    }

    public function saveTindakan($id = null)
    {
        $this->request->allowMethod(['post']);

        $Exams = $this->fetchTable('RadiologyExaminations');
        $Items = $this->fetchTable('RadiologyExaminationItems');
        $Supplies = $this->fetchTable('RadiologyExaminationSupplies');

        $d = $this->request->getData();
        $back = $id ? ['action' => 'tindakan', $id] : ['action' => 'tindakan'];

        $num = fn($v) => is_numeric($v) ? (float)$v : 0.0;
        $nul = fn($v) => ($v === null || $v === '') ? null : $v;

        // ---- baris tindakan ----
        $itemRows = [];
        $txTotal = 0.0;
        foreach ((array)($d['items'] ?? []) as $r) {
            if (trim((string)($r['examination_name'] ?? '')) === '') {
                continue;
            }
            $row = [
                'id' => (int)($r['id'] ?? 0),
                'code' => $nul($r['code'] ?? null),
                'examination_name' => trim((string)$r['examination_name']),
                'exam_type' => $nul($r['exam_type'] ?? null),
                'photo_no' => $nul($r['photo_no'] ?? null),
                'tariff' => $num($r['tariff'] ?? 0),
                'doctor_fee' => $num($r['doctor_fee'] ?? 0),
                'incentive' => $num($r['incentive'] ?? 0),
                'insurance_cover' => $num($r['insurance_cover'] ?? 0),
                'jpk' => $num($r['jpk'] ?? 0),
                'disc_rs' => $num($r['disc_rs'] ?? 0),
                'disc_dr' => $num($r['disc_dr'] ?? 0),
                'outside_doctor_fee' => $num($r['outside_doctor_fee'] ?? 0),
                'qty' => 1,
            ];
            // Total baris = Tarif RS + Jasa Dokter - Disk RS - Disk DR  (sesuaikan bila aturannya beda)
            $row['total'] = $row['tariff'] + $row['doctor_fee'] - $row['disc_rs'] - $row['disc_dr'];
            $txTotal += $row['total'];
            $itemRows[] = $row;
        }

        // ---- baris alkes / BHP ----
        $supRows = [];
        $supTotal = 0.0;
        foreach ((array)($d['supplies'] ?? []) as $r) {
            if (trim((string)($r['name'] ?? '')) === '') {
                continue;
            }
            $qty = $num($r['qty'] ?? 0);
            $price = $num($r['price'] ?? 0);
            $charged = !empty($r['charged_to_patient']) ? 1 : 0;
            $row = [
                'item_id' => $nul($r['item_id'] ?? null),
                'code' => $nul($r['code'] ?? null),
                'name' => trim((string)$r['name']),
                'qty' => $qty,
                'unit' => $nul($r['unit'] ?? null),
                'price' => $price,
                'total' => $qty * $price,
                'location' => $nul($r['location'] ?? null),
                'charged_to_patient' => $charged,
            ];
            if ($charged) {
                $supTotal += $row['total'];
            }
            $supRows[] = $row;
        }

        $patientName = trim((string)($d['patient_name'] ?? ''));
        if ($patientName === '' || !$itemRows) {
            $this->Flash->error('Nama pasien dan minimal satu tindakan wajib diisi.');
            return $this->redirect($back);
        }

        try {
            $receivedAt = new FrozenTime(trim(($d['tgl'] ?? date('Y-m-d')) . ' ' . ($d['jam'] ?? date('H:i:s'))));
        } catch (\Throwable $e) {
            $receivedAt = FrozenTime::now();
        }

        $header = [
            'patient_origin' => in_array($d['patient_origin'] ?? '', ['rs', 'luar'], true) ? $d['patient_origin'] : 'rs',
            'registration_id' => $nul($d['registration_id'] ?? null),
            'registration_no' => $nul($d['registration_no'] ?? null),
            'patient_id' => $nul($d['patient_id'] ?? null),
            'medical_record_no' => $nul($d['medical_record_no'] ?? null),
            'patient_name' => $patientName,
            'birth_date' => $nul($d['birth_date'] ?? null),
            'gender' => in_array($d['gender'] ?? '', ['L', 'P'], true) ? $d['gender'] : null,
            'patient_type' => $nul($d['patient_type'] ?? null),
            'class_name' => $nul($d['class_name'] ?? null),
            'reader_doctor_id' => $nul($d['reader_doctor_id'] ?? null),
            'sender_doctor_id' => $nul($d['sender_doctor_id'] ?? null),
            'sender_name' => $nul($d['sender_name'] ?? null),
            'officer_name' => $nul($d['officer_name'] ?? null),
            'request_type' => in_array($d['request_type'] ?? '', ['dikirim_dr', 'atas_permintaan_sendiri'], true) ? $d['request_type'] : 'dikirim_dr',
            'system_type' => in_array($d['system_type'] ?? '', ['rs', 'dirujuk'], true) ? $d['system_type'] : 'rs',
            'eorder_note' => $nul($d['eorder_note'] ?? null),
            'barcode_label' => $nul($d['barcode_label'] ?? null),
            'received_at' => $receivedAt,
            'total' => $txTotal + $supTotal,
        ];

        $ok = $Exams->getConnection()->transactional(function () use ($Exams, $Items, $Supplies, $id, $header, $itemRows, $supRows) {
            if ($id) {
                $exam = $Exams->get($id);
                $exam = $Exams->patchEntity($exam, $header);
                if ($exam->status === 'received') {
                    $exam->status = 'processing';
                }
            } else {
                $header['rad_no'] = $this->generateRadNo($Exams);
                $header['status'] = 'processing';
                $header['processed_by'] = $header['officer_name'];
                $exam = $Exams->newEntity($header);
            }

            if (!$Exams->save($exam)) {
                return false;
            }

            $keep = [];
            foreach ($itemRows as $row) {
                $ent = null;
                if ($row['id'] > 0) {
                    $ent = $Items->find()->where(['id' => $row['id'], 'examination_id' => $exam->id])->first();
                }
                unset($row['id']);
                $ent = $ent ?: $Items->newEmptyEntity();
                $ent = $Items->patchEntity($ent, $row);
                $ent->examination_id = $exam->id;
                if (!$Items->save($ent)) {
                    return false;
                }
                $keep[] = $ent->id;
            }
            if ($keep) {
                $Items->deleteAll(['examination_id' => $exam->id, 'id NOT IN' => $keep]);
            }

            $Supplies->deleteAll(['examination_id' => $exam->id]);
            foreach ($supRows as $row) {
                $ent = $Supplies->newEntity($row);
                $ent->examination_id = $exam->id;
                if (!$Supplies->save($ent)) {
                    return false;
                }
            }

            $this->savedId = $exam->id;
            return true;
        });

        if (!$ok) {
            $this->Flash->error('Gagal menyimpan data tindakan.');
            return $this->redirect($back);
        }

        $this->Flash->success('Data tindakan radiologi disimpan.');
        return $this->redirect(['action' => 'tindakan', $this->savedId]);
    }

    /** @var int|null */
    private $savedId = null;

    // =====================================================================
    // INFORMASI BIAYA PASIEN (JSON)
    // GET: registration_no (atau registration_id), sampai (opsional, Y-m-d)
    // =====================================================================
    public function biaya()
    {
        $regNo = trim((string)$this->request->getQuery('registration_no', ''));
        $regId = $this->request->getQuery('registration_id');
        $sampai = trim((string)$this->request->getQuery('sampai', ''));

        $R = $this->fetchTable('Registrations');
        $regField = $this->pick($R, ['registration_no', 'registration_number']) ?? 'registration_no';
        $dateField = $this->pick($R, ['registration_date', 'admission_date', 'tgl_masuk', 'tgl_daftar', 'created']);

        $q = $R->find()->contain(['Patients']);
        if ($regId) {
            $q->where(['Registrations.id' => (int)$regId]);
        } elseif ($regNo !== '') {
            $q->where(['Registrations.' . $regField => $regNo]);
        } else {
            return $this->jsonOut(['error' => 'No registrasi kosong.'])->withStatus(422);
        }

        $reg = $q->first();
        if (!$reg) {
            return $this->jsonOut(['error' => 'Registrasi tidak ditemukan.'])->withStatus(404);
        }

        $p = $reg->patient;
        $masuk = $dateField ? $reg->{$dateField} : null;

        $patient = [
            'registration_no' => (string)($reg->{$regField} ?? ''),
            'rm' => $p->code ?? '',
            'name' => $p->full_name ?? '',
            'tgl_masuk' => $masuk ? (is_object($masuk) ? $masuk->format('Y-m-d') : substr((string)$masuk, 0, 10)) : '',
            'jam_masuk' => !empty($reg->admission_time)
                ? (is_object($reg->admission_time) ? $reg->admission_time->format('H:i') : substr((string)$reg->admission_time, 0, 5))
                : '',
        ];

        // ---- Tindakan radiologi + BHP yang dibebankan ke pasien ----
        $Exams = $this->fetchTable('RadiologyExaminations');
        $eq = $Exams->find()
            ->contain(['RadiologyExaminationItems', 'RadiologyExaminationSupplies'])
            ->where(['OR' => [
                'RadiologyExaminations.registration_id' => $reg->id,
                'RadiologyExaminations.registration_no' => $patient['registration_no'],
            ]])
            ->order(['RadiologyExaminations.received_at' => 'ASC']);
        if ($sampai !== '') {
            $eq->where(['RadiologyExaminations.received_at <=' => $sampai . ' 23:59:59']);
        }

        $tindakan = [];
        foreach ($eq->all() as $e) {
            $tgl = $e->received_at ? $e->received_at->format('Y-m-d') : '';
            foreach ($e->radiology_examination_items as $it) {
                $tindakan[] = [
                    'no' => $e->rad_no,
                    'nama' => $it->examination_name,
                    'tgl' => $tgl,
                    'total' => (float)$it->total,
                    'cat' => 'ro',
                ];
            }
            foreach ($e->radiology_examination_supplies as $s) {
                if (!$s->charged_to_patient) {
                    continue;
                }
                $tindakan[] = [
                    'no' => $e->rad_no,
                    'nama' => $s->name . ' (' . (float)$s->qty . ' ' . $s->unit . ')',
                    'tgl' => $tgl,
                    'total' => (float)$s->total,
                    'cat' => 'ro',
                ];
            }
        }

        // TODO: kamar rawat inap, lab, bedah, resep, dan pembayaran/uang muka
        // diisi setelah struktur tabelnya diterima. Format barisnya:
        //  kamar: ruang, kelas, tgl_masuk, tgl_keluar, tarif, hari, total
        //  bayar: no, tgl, jumlah
        return $this->jsonOut([
            'patient' => $patient,
            'kamar' => [],
            'tindakan' => $tindakan,
            'bayar' => [],
        ]);
    }

    // =====================================================================
    // AJAX (JSON)
    // =====================================================================
    public function searchMaster()
    {
        $q = trim((string)$this->request->getQuery('q', ''));
        $T = $this->fetchTable('MasterRadiologies');

        $code = $this->pick($T, ['code', 'kode', 'radiology_code']);
        $name = $this->pick($T, ['name', 'examination_name', 'radiology_name', 'nama']);
        $tariff = $this->pick($T, ['tariff', 'tarif', 'price', 'total_tariff', 'total']);
        $cat = $this->pick($T, ['category', 'kategori', 'group_name', 'type']);
        $active = $this->pick($T, ['is_active', 'active']);

        if (!$name) {
            return $this->jsonOut([]);
        }

        $query = $T->find()->limit(30);
        if ($active) {
            $query->where([$active => 1]);
        }
        if ($q !== '') {
            $or = [];
            foreach ([$code, $name] as $f) {
                if ($f) {
                    $or[$f . ' LIKE'] = '%' . $q . '%';
                }
            }
            $query->where(['OR' => $or]);
        }

        $rows = [];
        foreach ($query->all() as $r) {
            $rows[] = [
                'id' => $r->id,
                'kode' => $code ? (string)$r->{$code} : '',
                'nama' => (string)$r->{$name},
                'tarif' => $tariff ? (float)$r->{$tariff} : 0,
                'kategori' => $cat ? (string)$r->{$cat} : '',
            ];
        }

        return $this->jsonOut($rows);
    }

    public function searchItems()
    {
        $q = trim((string)$this->request->getQuery('q', ''));
        $T = $this->fetchTable('PharmacyItems');

        $code = $this->pick($T, ['code', 'item_code']);
        $name = $this->pick($T, ['name', 'item_name']);
        $unit = $this->pick($T, ['unit', 'satuan']);
        $price = $this->pick($T, ['sell_price', 'selling_price', 'price', 'harga_jual']);

        if (!$name) {
            return $this->jsonOut([]);
        }

        $query = $T->find()->limit(30);
        if ($q !== '') {
            $or = [];
            foreach ([$code, $name] as $f) {
                if ($f) {
                    $or[$f . ' LIKE'] = '%' . $q . '%';
                }
            }
            $query->where(['OR' => $or]);
        }

        $rows = [];
        foreach ($query->all() as $r) {
            $rows[] = [
                'id' => $r->id,
                'kode' => $code ? (string)$r->{$code} : '',
                'nama' => (string)$r->{$name},
                'unit' => $unit ? (string)$r->{$unit} : '',
                'harga' => $price ? (float)$r->{$price} : 0,
            ];
        }

        return $this->jsonOut($rows);
    }

    public function searchRegistration()
    {
        $q = trim((string)$this->request->getQuery('q', ''));
        $R = $this->fetchTable('Registrations');
        $regNo = $this->pick($R, ['registration_no', 'registration_number']) ?? 'registration_no';
        $types = ['rawat_jalan' => 'Rawat Jalan', 'rawat_inap' => 'Rawat Inap', 'igd' => 'IGD'];

        $regs = $R->find()
            ->contain(['Patients'])
            ->where(['OR' => [
                'Registrations.' . $regNo . ' LIKE' => '%' . $q . '%',
                'Patients.full_name LIKE' => '%' . $q . '%',
                'Patients.code_hash' => \App\Utility\DataCipher::hash($q),
            ]])
            ->order(['Registrations.id' => 'DESC'])
            ->limit(20)
            ->all();

        $rows = [];
        foreach ($regs as $r) {
            $p = $r->patient;
            $rows[] = [
                'id' => $r->id,
                'registration_no' => (string)($r->{$regNo} ?? ''),
                'patient_id' => $r->patient_id,
                'rm' => $p->code ?? '',
                'name' => $p->full_name ?? '',
                'birth_date' => $this->dateOf($p->birth_date ?? null) ?? '',
                'gender' => $this->genderOf($p) ?? '',
                'service_label' => $types[$r->service_type ?? ''] ?? '',
            ];
        }

        return $this->jsonOut($rows);
    }

    // =====================================================================
    // HELPER
    // =====================================================================
    private function jsonOut(array $rows)
    {
        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($rows, JSON_UNESCAPED_UNICODE));
    }

    private function pick($table, array $candidates): ?string
    {
        $schema = $table->getSchema();
        foreach ($candidates as $c) {
            if ($schema->hasColumn($c)) {
                return $c;
            }
        }
        return null;
    }

    private function dateOf($v): ?string
    {
        if (empty($v)) {
            return null;
        }
        return is_object($v) ? $v->format('Y-m-d') : (string)$v;
    }

    private function genderOf($p): ?string
    {
        if (!$p) {
            return null;
        }
        $g = strtoupper(trim((string)($p->gender ?? $p->sex ?? $p->jenis_kelamin ?? '')));
        if (in_array($g, ['L', 'M', 'MALE', 'PRIA', 'LAKI-LAKI', '1'], true)) {
            return 'L';
        }
        if (in_array($g, ['P', 'F', 'FEMALE', 'WANITA', 'PEREMPUAN', '2'], true)) {
            return 'P';
        }
        return null;
    }

    private function officerName(): string
    {
        $identity = $this->request->getAttribute('identity');
        return $identity ? (string)($identity->get('name') ?? $identity->get('username') ?? '-') : '-';
    }

    private function doctorOptions(): array
    {
        $T = $this->fetchTable('Doctors');
        $nameF = $this->pick($T, ['doctor_name', 'name']) ?? 'name';
        $out = [];
        foreach ($T->find()->select(['id', $nameF])->order([$nameF => 'ASC'])->all() as $r) {
            $out[$r->id] = (string)$r->{$nameF};
        }
        return $out;
    }

    /** Format SIMRS lama: R + tahun + urut 11 digit (R202600000003743). */
    private function generateRadNo($Exams): string
    {
        $prefix = 'R' . date('Y');
        $last = $Exams->find()
            ->select(['rad_no'])
            ->where(['rad_no LIKE' => $prefix . '%'])
            ->order(['rad_no' => 'DESC'])
            ->first();

        $seq = $last ? ((int)substr($last->rad_no, strlen($prefix))) + 1 : 1;

        return $prefix . str_pad((string)$seq, 11, '0', STR_PAD_LEFT);
    }
}