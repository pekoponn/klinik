<?php
declare(strict_types=1);

namespace App\Controller\Farmasi;

use App\Controller\AppController;

class OrdersController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Flash');

        $this->Orders = $this->fetchTable('Orders');
        $this->Registrations = $this->fetchTable('Registrations');
        $this->Patients = $this->fetchTable('Patients');
        $this->Doctors = $this->fetchTable('Doctors');
        $this->OrderStatuses = $this->fetchTable('OrderStatuses');
        $this->OrderPrescriptions = $this->fetchTable('OrderPrescriptions');
        $this->Medicines = $this->fetchTable('Medicines');
        $this->Users = $this->fetchTable('Users');
        $this->OrderLaboratories = $this->fetchTable('OrderLaboratories');
        $this->MasterLaboratories = $this->fetchTable('MasterLaboratories');
        $this->OrderRadiologies = $this->fetchTable('OrderRadiologies');
        $this->MasterRadiologies = $this->fetchTable('MasterRadiologies');
    }

    public function index()
    {
        $q = trim((string)$this->request->getQuery('q', ''));
        $dateFrom = $this->request->getQuery('date_from', date('Y-m-d'));
        $dateTo = $this->request->getQuery('date_to', date('Y-m-d'));
        $statusId = $this->request->getQuery('status_id', '');
        $depo = $this->request->getQuery('depo', '');

        if (empty($dateFrom)) {
            $dateFrom = date('Y-m-d');
        }

        if (empty($dateTo)) {
            $dateTo = date('Y-m-d');
        }

        if ($dateTo < $dateFrom) {
            $dateTo = $dateFrom;
        }

        $query = $this->Orders->find()
            ->contain(['Patients', 'Doctors', 'OrderStatuses', 'Registrations'])
            ->where([
                'DATE(Orders.tgl_order) >=' => $dateFrom,
                'DATE(Orders.tgl_order) <=' => $dateTo,
            ]);

        if ($q !== '') {
            $query->where([
                'OR' => [
                    'Orders.no_order LIKE' => '%' . $q . '%',
                    'Patients.code_hash' => \App\Utility\DataCipher::hash((string)$q),
                    'Patients.full_name LIKE' => '%' . $q . '%',
                    'Doctors.doctor_name LIKE' => '%' . $q . '%',
                    'Doctors.name LIKE' => '%' . $q . '%',
                    'Registrations.registration_no LIKE' => '%' . $q . '%',
                    'Registrations.registration_number LIKE' => '%' . $q . '%',
                    'Orders.lokasi_depo LIKE' => '%' . $q . '%',
                ],
            ]);
        }

        if ($statusId !== '' && $statusId !== null) {
            $query->where(['Orders.status_id' => $statusId]);
        }

        if ($depo !== '' && $depo !== null) {
            $query->where(['Orders.lokasi_depo' => $depo]);
        }

        $this->paginate = [
            'contain' => ['Patients', 'Doctors', 'OrderStatuses', 'Registrations'],
            'order' => ['Orders.tgl_order' => 'DESC', 'Orders.id' => 'DESC'],
            'limit' => 30,
        ];

        $orders = $this->paginate($query);

        $statuses = $this->OrderStatuses->find('list', [
            'keyField' => 'id',
            'valueField' => 'nama',
        ])
            ->order(['OrderStatuses.urutan' => 'ASC', 'OrderStatuses.id' => 'ASC'])
            ->toArray();

        $depoList = $this->Orders->find()
            ->select(['lokasi_depo'])
            ->where([
                'lokasi_depo IS NOT' => null,
                'lokasi_depo !=' => '',
            ])
            ->distinct(['lokasi_depo'])
            ->order(['lokasi_depo' => 'ASC'])
            ->enableHydration(false)
            ->all()
            ->extract('lokasi_depo')
            ->toList();

        if (empty($depoList)) {
            $depoList = [
                'FARMASI 2 RAJAL DEPO',
            ];
        }

        $this->set(compact(
            'orders',
            'q',
            'dateFrom',
            'dateTo',
            'statusId',
            'depo',
            'statuses',
            'depoList'
        ));
    }

public function antrianFarmasi()
{
    $dateFrom = $this->request->getQuery('date_from', date('Y-m-d'));

    if (empty($dateFrom)) {
        $dateFrom = date('Y-m-d');
    }

    $query = $this->Orders->find()
        ->contain(['Patients', 'Doctors', 'Registrations'])
        ->innerJoinWith('OrderPrescriptions')
        ->innerJoinWith('Registrations')
        ->where([
            'DATE(Orders.tgl_order) >=' => $dateFrom,
            'Orders.status_id' => 1, // status "Baru" / belum diproses farmasi
            'Registrations.service_type IN' => ['rawat_jalan'],
        ])
        ->group(['Orders.id'])
        ->order(['Orders.tgl_order' => 'ASC', 'Orders.id' => 'ASC']);

    $orders = $query->all();

    // Ambil No Rekam Medis per registration_id sekali jalan (hindari query di dalam loop).
    $registrationIds = [];
    foreach ($orders as $o) {
        if (!empty($o->registration_id)) {
            $registrationIds[] = (int)$o->registration_id;
        }
    }

    $medicalRecordMap = [];
    if (!empty($registrationIds)) {
        $this->MedicalRecords = $this->fetchTable('MedicalRecords');

        $medicalRecords = $this->MedicalRecords->find()
            ->select(['registration_id', 'medical_record_no'])
            ->where(['registration_id IN' => array_unique($registrationIds)])
            ->enableHydration(false)
            ->all();

        foreach ($medicalRecords as $mr) {
            $medicalRecordMap[(int)$mr['registration_id']] = $mr['medical_record_no'];
        }
    }

    $rows = [];
    $noAntri = 0;

    foreach ($orders as $o) {
        $noAntri++;
        $rows[] = [
            'no_antri' => 'A-' . str_pad((string)$noAntri, 4, '0', STR_PAD_LEFT),
            'order' => $o,
            'medical_record_no' => $medicalRecordMap[(int)$o->registration_id] ?? '-',
        ];
    }

    $sisaResep = count($rows);

    $this->set(compact('rows', 'dateFrom', 'sisaResep'));
}

public function proses($id = null)
{
    // ── MODE 1: Resep Luar (langsung dari sidebar, tanpa order/registrasi) ──
    // Dipakai ketika pegawai/pasien mau beli obat langsung tanpa lewat
    // pendaftaran/registrasi. Order tetap dibuat & obat tetap dicatat
    // dari master obat (stok/pembelian), cuma tidak terikat ke
    // patient_id / registration_id.
    $isResepLuar = empty($id);

    $patient = null;
    $doctor = null;
    $registration = null;
    $medicalRecordNo = '-';

    if ($isResepLuar) {
        $order = $this->Orders->newEmptyEntity();
        $order->tgl_order = new \Cake\I18n\FrozenTime();
        $order->lokasi_depo = 'FARMASI 2 RAJAL DEPO';
        $order->status_id = 1;
    } else {
        $order = $this->Orders->find()
            ->contain([
                'Patients',
                'Doctors',
                'Registrations',
                'OrderPrescriptions' => ['Medicines'],
            ])
            ->where(['Orders.id' => (int)$id])
            ->first();

        if (!$order) {
            $this->Flash->error('Order tidak ditemukan.');
            return $this->redirect(['action' => 'antrianFarmasi']);
        }

        $patient = $order->patient ?? null;
        $doctor = $order->doctor ?? null;
        $registration = $order->registration ?? null;

        // Ambil No Rekam Medis dari registration_id order ini.
        if (!empty($order->registration_id)) {
            $this->MedicalRecords = $this->fetchTable('MedicalRecords');
            $mr = $this->MedicalRecords->find()
                ->select(['medical_record_no'])
                ->where(['registration_id' => (int)$order->registration_id])
                ->first();

            if ($mr) {
                $medicalRecordNo = $mr->medical_record_no;
            }
        }
    }

    if ($this->request->is('post')) {
        $data = $this->request->getData();

        $identity = $this->request->getAttribute('identity');
        $userId = $identity ? ($identity->id ?? null) : null;

        if ($isResepLuar) {
            // ── SUBMIT RESEP LUAR: buat order baru dari nol ──
            $namaPembeli = trim((string)($data['nama_pembeli'] ?? ''));

            if ($namaPembeli === '') {
                $this->Flash->error('Nama pembeli wajib diisi untuk resep luar.');
                return $this->redirect($this->referer());
            }

            $rows = $data['prescriptions'] ?? [];
            $prescriptions = [];

            foreach ($rows as $row) {
                $medicineId = !empty($row['medicine_id']) ? (int)$row['medicine_id'] : 0;

                if ($medicineId <= 0) {
                    continue;
                }

                if (!$this->Medicines->exists(['Medicines.id' => $medicineId])) {
                    continue;
                }

                $qty = (float)($row['qty_tagih'] ?? 0);

                if ($qty <= 0) {
                    continue;
                }

                $harga = (float)($row['harga'] ?? 0);

                $rxSchema = $this->OrderPrescriptions->getSchema();

                $prescriptionRow = [
                    'medicine_id' => $medicineId,
                    'qty_satuan' => $qty,
                    'harga' => $harga,
                    'total_harga' => $qty * $harga,
                    'aturan_pakai' => trim((string)($row['aturan_pakai'] ?? '')),
                ];

                if ($rxSchema->hasColumn('waktu_minum')) {
                    $prescriptionRow['waktu_minum'] = trim((string)($row['waktu_minum'] ?? ''));
                }

                if ($rxSchema->hasColumn('is_kronis')) {
                    $prescriptionRow['is_kronis'] = !empty($row['is_kronis']) ? 1 : 0;
                }

                $prescriptions[] = $prescriptionRow;
            }

            if (empty($prescriptions)) {
                $this->Flash->error('Pilih minimal 1 obat untuk resep luar.');
                return $this->redirect($this->referer());
            }

            $order->patient_id = null;
            $order->registration_id = null;
            $order->doctor_id = null;
            $order->pesan = 'RESEP LUAR';
            $order->tgl_order = date('Y-m-d H:i:s');
            $order->no_order = $this->generateOrderNumber();
            $order->status_id = 1;
            $order->created_by = !empty($userId) ? (int)$userId : 1;

            // Simpan nama pembeli. Kalau kolom 'nama_pembeli' belum ada di
            // tabel orders, dicatat sementara di kolom 'catatan' supaya
            // tidak error dan tetap kelihatan di history/detail order.
            $schema = $this->Orders->getSchema();
            if ($schema->hasColumn('nama_pembeli')) {
                $order->nama_pembeli = $namaPembeli;
            } else {
                $order->catatan = 'Pembeli (Resep Luar): ' . $namaPembeli;
            }

            if (!$this->Orders->save($order)) {
                $this->Flash->error('Gagal menyimpan order resep luar. Silakan coba lagi.');
                return $this->redirect($this->referer());
            }

            foreach ($prescriptions as $prescription) {
                $prescription['order_id'] = $order->id;
                $entity = $this->OrderPrescriptions->newEntity($prescription);

                if (!$this->OrderPrescriptions->save($entity)) {
                    continue;
                }

                // TODO: potong stok obat & catat sebagai transaksi
                // pembelian di sini sesuai struktur tabel stok/pembelian
                // yang dipakai (mis. StockMovements / PharmacyItems),
                // begitu skema stoknya dikonfirmasi.
            }

            // Resep luar dianggap langsung selesai/terbayar saat itu juga.
            $nextStatus = $this->OrderStatuses->find()
                ->where([
                    'OR' => [
                        'nama LIKE' => '%proses%',
                        'nama LIKE' => '%selesai%',
                    ],
                ])
                ->order(['urutan' => 'ASC'])
                ->first();

            $order->status_id = $nextStatus ? $nextStatus->id : 2;
            $this->Orders->save($order);

            $this->Flash->success('Resep luar berhasil diposting. No Order: ' . $order->no_order);

            return $this->redirect(['action' => 'proses']);
        }

        // ── SUBMIT ORDER TERDAFTAR (alur lama, dari Antrian Farmasi) ──
        $rows = $data['prescriptions'] ?? [];

        $rxSchema = $this->OrderPrescriptions->getSchema();

        foreach ($order->order_prescriptions as $rx) {
            $rowId = (string)$rx->id;

            if (!isset($rows[$rowId])) {
                continue;
            }

            $qtyTagih = (float)($rows[$rowId]['qty_tagih'] ?? $rx->qty_satuan);
            $harga = (float)$rx->harga;

            $rx->qty_satuan = $qtyTagih;
            $rx->total_harga = $qtyTagih * $harga;

            if ($rxSchema->hasColumn('waktu_minum')) {
                $rx->waktu_minum = trim((string)($rows[$rowId]['waktu_minum'] ?? ''));
            }

            if ($rxSchema->hasColumn('is_kronis')) {
                $rx->is_kronis = !empty($rows[$rowId]['is_kronis']) ? 1 : 0;
            }

            $this->OrderPrescriptions->save($rx);
        }

        $nextStatus = $this->OrderStatuses->find()
            ->where([
                'OR' => [
                    'nama LIKE' => '%proses%',
                    'nama LIKE' => '%selesai%',
                ],
            ])
            ->order(['urutan' => 'ASC'])
            ->first();

        $order->status_id = $nextStatus ? $nextStatus->id : 2;

        if ($this->Orders->save($order)) {
            $this->Flash->success('Resep berhasil di-posting. No Order: ' . $order->no_order);
            return $this->redirect(['action' => 'antrianFarmasi']);
        }

        $this->Flash->error('Gagal posting resep. Silakan coba lagi.');
        return $this->redirect(['action' => 'proses', $order->id]);
    }

    $this->set(compact('order', 'medicalRecordNo', 'isResepLuar', 'patient', 'doctor', 'registration'));
}

    public function form($registrationId = null)
    {
        $registration = null;
        $registrationNo = trim((string)$this->request->getQuery('registration_no', ''));

        if (!empty($registrationId) && is_numeric($registrationId)) {
            $registration = $this->Registrations->find()
                ->contain(['Patients'])
                ->where(['Registrations.id' => (int)$registrationId])
                ->first();
        }

        if (empty($registration) && $registrationNo !== '') {
            $registration = $this->Registrations->find()
                ->contain(['Patients'])
                ->where([
                    'OR' => [
                        'Registrations.registration_no' => $registrationNo,
                        'Registrations.registration_number' => $registrationNo,
                    ],
                ])
                ->first();
        }

        if (empty($registration)) {
            $this->Flash->error('No Registrasi tidak ditemukan. Buka E-Order dari EMR pasien.');
            return $this->redirect(['action' => 'index']);
        }

        $patient = $registration->patient ?? null;

        if (empty($patient)) {
            $this->Flash->error('Data pasien dari registrasi ini tidak ditemukan.');
            return $this->redirect(['action' => 'index']);
        }

        $historyOrders = $this->Orders->find()
            ->contain(['OrderStatuses'])
            ->where(['Orders.patient_id' => $patient->id])
            ->order(['Orders.tgl_order' => 'DESC'])
            ->all();

        $order = $this->Orders->newEmptyEntity();
        $order->registration_id = $registration->id;
        $order->patient_id = $patient->id;
        $order->doctor_id = null;
        $order->tgl_order = date('Y-m-d H:i:s');
        $order->lokasi_depo = 'FARMASI 2 RAJAL DEPO';

        $identity = $this->request->getAttribute('identity');
        $userId = $identity ? ($identity->id ?? null) : null;
        $createdByName = 'Admin';

        if (!empty($userId)) {
            $user = $this->Users->find()
                ->where(['Users.id' => $userId])
                ->first();

            if ($user) {
                $createdByName = $user->name
                    ?? $user->full_name
                    ?? $user->username
                    ?? $user->email
                    ?? 'Admin';
            }
        }

        if ($this->request->is('post')) {
            $data = $this->request->getData();
        
            $data['pesan'] = $this->request->getData('pesan') ?? null;
            $data['registration_id'] = $registration->id;
            $data['patient_id'] = $patient->id;
            $data['doctor_id'] = !empty($data['doctor_id']) ? (int)$data['doctor_id'] : null;
            $data['tgl_order'] = date('Y-m-d H:i:s');

            if (!empty($userId)) {
                $data['created_by'] = (int)$userId;
            }

            $order = $this->Orders->patchEntity($order, $data);
            $order->pesan = $data['pesan'] ?? null;

            if (empty($order->no_order)) {
                $order->no_order = $this->generateOrderNumber();
            }

            $order->created_by = !empty($userId) ? (int)$userId : 1;
            $order->status_id = 1;

            $prescriptions = [];

            if (!empty($data['prescriptions'])) {
                foreach ($data['prescriptions'] as $row) {
                    $medicineId = !empty($row['medicine_id']) ? (int)$row['medicine_id'] : 0;

                    if ($medicineId <= 0) {
                        continue;
                    }

                    if (!$this->Medicines->exists(['Medicines.id' => $medicineId])) {
                        continue;
                    }

                    $totalHarga = $row['total_harga'] ?? 0;
                    $totalHarga = str_replace(['Rp', '.', ','], ['', '', '.'], (string)$totalHarga);

                    $prescriptions[] = [
                        'medicine_id' => $medicineId,
                        'is_kronis' => !empty($row['is_kronis']) ? 1 : 0,
                        'jml_per_hari' => (float)($row['jml_per_hari'] ?? 0),
                        'qty_min' => (float)($row['qty_min'] ?? 0),
                        'jml_hari' => (int)($row['jml_hari'] ?? 0),
                        'qty_satuan' => (float)($row['qty_satuan'] ?? 0),
                        'aturan_pakai' => $row['aturan_pakai'] ?? '',
                        'harga' => (float)($row['harga'] ?? 0),
                        'total_harga' => (float)$totalHarga,
                        'keterangan' => $row['keterangan'] ?? '',
                    ];
                }
            }

            $laboratories = [];

            if (!empty($data['laboratories'])) {
                foreach ($data['laboratories'] as $row) {
                    $labName = trim((string)($row['lab_name'] ?? ''));

                    if ($labName === '') {
                        continue;
                    }

                    $tariff = $row['tariff'] ?? 0;
                    $tariff = str_replace(['Rp', '.', ','], ['', '', '.'], (string)$tariff);

                    $laboratories[] = [
                        'code' => trim((string)($row['lab_code'] ?? '')),
                        'examination_name' => $labName,
                        'price' => (float)$tariff,
                        'approval_note' => $row['approval_note'] ?? '',
                    ];
                }
            }

            $radiologies = [];

            if (!empty($data['radiologies'])) {
                foreach ($data['radiologies'] as $row) {
                    $radioName = trim((string)($row['examination_name'] ?? ''));

                    if ($radioName === '') {
                        continue;
                    }

                    $tariff = $row['tariff'] ?? 0;
                    $tariff = str_replace(['Rp', '.', ','], ['', '', '.'], (string)$tariff);

                    $qty = !empty($row['qty']) ? (int)$row['qty'] : 1;
                    if ($qty <= 0) {
                        $qty = 1;
                    }

                    $total = $row['total'] ?? ((float)$tariff * $qty);
                    $total = str_replace(['Rp', '.', ','], ['', '', '.'], (string)$total);

                    $radiologies[] = [
                        'registration_id' => $registration->id,
                        'patient_id' => $patient->id,
                        'doctor_id' => !empty($data['doctor_id']) ? (int)$data['doctor_id'] : null,
                        'code' => trim((string)($row['code'] ?? '')),
                        'examination_name' => $radioName,
                        'tariff' => (float)$tariff,
                        'qty' => $qty,
                        'total' => (float)$total,
                        'approval_note' => $row['approval_note'] ?? '',
                        'notes' => $row['notes'] ?? '',
                        'status' => 'draft',
                        'is_active' => 1,
                        'is_delete' => 0,
                    ];
                }
            }

            if (
                empty($prescriptions)
                && empty($data['obat_lain'])
                && empty($laboratories)
                && empty($radiologies)
            ) {
                $this->Flash->error('Pilih obat, pemeriksaan laboratorium, atau pemeriksaan radiologi terlebih dahulu.');
                return $this->redirect($this->referer());
            }

            if (!$this->Orders->save($order)) {
                debug($order->getErrors());
                die;
            }

            foreach ($prescriptions as $prescription) {
                $prescription['order_id'] = $order->id;
                $entity = $this->OrderPrescriptions->newEntity($prescription);

                if (!$this->OrderPrescriptions->save($entity)) {
                    debug($entity);
                    debug($entity->getErrors());
                    die;
                }
            }

            foreach ($laboratories as $laboratory) {
                $laboratory['order_id'] = $order->id;
                $entity = $this->OrderLaboratories->newEntity($laboratory);

                if (!$this->OrderLaboratories->save($entity)) {
                    debug($entity);
                    debug($entity->getErrors());
                    die;
                }
            }

            foreach ($radiologies as $radiology) {
                $radiology['order_id'] = $order->id;
                $entity = $this->OrderRadiologies->newEntity($radiology);

                if (!$this->OrderRadiologies->save($entity)) {
                    debug($entity);
                    debug($entity->getErrors());
                    die;
                }
            }

            $this->Flash->success('Order berhasil dibuat. No Order: ' . $order->no_order);

            return $this->redirect([
                'action' => 'form',
                $registration->id
            ]);
        }

        $doctorNameField = $this->Doctors->getSchema()->hasColumn('doctor_name') ? 'doctor_name' : 'name';

        $doctorList = $this->Doctors->find('list', [
            'keyField' => 'id',
            'valueField' => $doctorNameField,
        ])
            ->order(['Doctors.' . $doctorNameField => 'ASC'])
            ->toArray();

        $statuses = $this->OrderStatuses->find('list', [
            'keyField' => 'id',
            'valueField' => 'nama',
        ])
            ->order(['urutan' => 'ASC'])
            ->toArray();

        $depoList = [
            'FARMASI 2 RAJAL DEPO',
        ];

        $this->OutpatientVisits = $this->fetchTable('OutpatientVisits');

        $outpatientVisit = null;

        $outpatientVisit = $this->OutpatientVisits->find()
            ->where(['OutpatientVisits.registration_id' => $registration->id])
            ->first();

            $this->set(compact(
                'order',
                'registration',
                'patient',
                'historyOrders',
                'statuses',
                'depoList',
                'doctorList',
                'createdByName',
                'outpatientVisit'
            ));
    }

    public function detail($id = null)
    {
        if (empty($id)) {
            $this->Flash->error('ID order tidak ditemukan.');
            return $this->redirect(['action' => 'index']);
        }

        $order = $this->Orders->get($id, [
            'contain' => [
                'Patients',
                'Doctors',
                'OrderStatuses',
                'Registrations',
                'OrderPrescriptions' => ['Medicines'],
                'OrderLaboratories',
                'OrderRadiologies',
            ],
        ]);

        $this->set(compact('order'));
    }

    public function historyPasien($patientId = null)
    {
        $this->request->allowMethod(['get']);
        $this->autoRender = false;

        if (empty($patientId)) {
            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode([]));
        }

        $rows = $this->Orders->find()
            ->contain(['OrderStatuses'])
            ->where(['Orders.patient_id' => $patientId])
            ->order(['Orders.tgl_order' => 'DESC'])
            ->limit(50)
            ->all();

        $results = [];

        foreach ($rows as $o) {
            $results[] = [
                'id' => $o->id,
                'no_order' => $o->no_order,
                'tgl_order' => !empty($o->tgl_order) ? $o->tgl_order->format('d-m-Y H:i') : '-',
                'status' => $o->order_status->nama ?? '-',
                'warna' => $o->order_status->warna ?? '#6c757d',
            ];
        }

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($results, JSON_UNESCAPED_UNICODE));

    }

    public function historyOrderDetail($id = null)
{
    $this->request->allowMethod(['get']);
    $this->autoRender = false;

    if (empty($id) || !is_numeric($id)) {
        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode([
                'success' => false,
                'message' => 'ID order tidak valid.',
            ], JSON_UNESCAPED_UNICODE));
    }

    try {
        $fmtDateOrder = function ($v) {
            if (empty($v)) return '-';
            return is_object($v) && method_exists($v, 'format') ? $v->format('d-m-Y') : (string)$v;
        };
        $fmtTimeOrder = function ($v) {
            if (empty($v)) return '-';
            return is_object($v) && method_exists($v, 'format') ? $v->format('H:i:s') : (string)$v;
        };

        $order = $this->Orders->find()
            ->contain([
                'Patients',
                'Doctors',
                'OrderStatuses',
                'Registrations',
                'OrderPrescriptions' => ['Medicines'],
                'OrderLaboratories',
                'OrderRadiologies',
            ])
            ->where(['Orders.id' => (int)$id])
            ->first();

        if (!$order) {
            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => 'Order tidak ditemukan.',
                ], JSON_UNESCAPED_UNICODE));
        }

        $patient = $order->patient ?? null;
        $doctor = $order->doctor ?? null;
        $registration = $order->registration ?? null;
        $status = $order->order_status ?? null;

        $serviceType = $registration->service_type ?? '-';

        if ($serviceType === 'rawat_jalan') {
            $asalPasien = 'Rawat Jalan';
        } else {
            $asalPasien = ucwords(str_replace('_', ' ', (string)$serviceType));
        }

        $items = [];

        foreach (($order->order_prescriptions ?? []) as $rx) {
            $medicine = $rx->medicine ?? null;

            $items[] = [
                'kode' => $medicine->code ?? '-',
                'nama' => $medicine->name ?? '-',
                'generic' => $medicine->generic_name ?? '',
                'satuan' => $medicine->unit_1 ?? '-',
                'is_kronis' => !empty($rx->is_kronis) ? 1 : 0,
                'jml_per_hari' => $rx->jml_per_hari ?? 0,
                'qty_min' => $rx->qty_min ?? 0,
                'jml_hari' => $rx->jml_hari ?? 0,
                'qty_satuan' => $rx->qty_satuan ?? 0,
                'aturan_pakai' => $rx->aturan_pakai ?? '',
                'harga' => (float)($rx->harga ?? 0),
                'total_harga' => (float)($rx->total_harga ?? 0),
            ];
        }

        $labs = [];

        foreach (($order->order_laboratories ?? []) as $lab) {
            $labs[] = [
                'kode' => $lab->code ?? '',
                'nama' => $lab->examination_name ?? '',
                'tarif' => (float)($lab->price ?? 0),
                'approval' => $lab->approval_note ?? '',
                'notes' => '',
            ];
        }

        $radios = [];

        foreach (($order->order_radiologies ?? []) as $radio) {
            $radios[] = [
                'kode' => $radio->code ?? '',
                'nama' => $radio->examination_name ?? '',
                'tarif' => (float)($radio->tariff ?? 0),
                'qty' => (int)($radio->qty ?? 1),
                'total' => (float)($radio->total ?? 0),
                'approval' => $radio->approval_note ?? '',
                'notes' => $radio->notes ?? '',
            ];
        }

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode([
                'success' => true,
                'order' => [
                    'id' => $order->id,
                    'no_order' => $order->no_order ?? '-',
                    'tgl_order' => $fmtDateOrder($order->tgl_order),
                    'jam_order' => $fmtTimeOrder($order->tgl_order),
                    'lokasi_depo' => $order->lokasi_depo ?? '-',
                    'pesan' => $order->pesan ?? '',
                    'catatan' => $order->catatan ?? '',
                    'doctor_id' => $order->doctor_id ?? '',
                    'doctor_name' => $doctor->doctor_name ?? $doctor->name ?? $doctor->nama ?? '-',
                    'status' => $status->nama ?? '-',
                    'status_warna' => $status->warna ?? '#6c757d',
                ],
                'patient' => [
                    'id' => $patient->id ?? '',
                    'medical_record_no' => $patient->code ?? '-',
                    'nama' => $patient->full_name ?? '-',
                    'jaminan' => $registration->insurance_type ?? $registration->payment_method ?? 'UMUM',
                    'asal_pasien' => $asalPasien,
                ],
                'items' => $items,
                'labs' => $labs,
                'radios' => $radios,
            ], JSON_UNESCAPED_UNICODE));
    } catch (\Throwable $e) {
        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], JSON_UNESCAPED_UNICODE));
    }
}

    public function cariPasien()
    {
        $this->request->allowMethod(['get']);
        $this->autoRender = false;

        $q = trim((string)$this->request->getQuery('q', ''));
        $results = [];

        if (strlen($q) >= 2) {
            $rows = $this->Patients->find()
                ->where([
                    'OR' => [
                        'Patients.code_hash' => \App\Utility\DataCipher::hash((string)$q),
                        'Patients.full_name LIKE' => '%' . $q . '%',
                    ],
                ])
                ->limit(20)
                ->all();

            foreach ($rows as $p) {
                $results[] = [
                    'id' => $p->id,
                    'medical_record_no' => $p->code ?? '',
                    'nama' => $p->full_name ?? '',
                    'jaminan' => $p->jaminan ?? 'Umum',
                ];
            }
        }

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($results, JSON_UNESCAPED_UNICODE));
    }

    public function cariObat()
    {
        $this->request->allowMethod(['get']);
        $this->autoRender = false;

        $q = trim((string)$this->request->getQuery('q', ''));
        $results = [];

        // q kosong (baru buka modal) -> tampilkan daftar obat (dibatasi 200 biar tetap ringan).
        // q diisi -> filter sesuai kata kunci, limit lebih kecil.
        $query = $this->Medicines->find()
            ->order(['Medicines.name' => 'ASC'])
            ->limit($q === '' ? 200 : 30);

        if ($q !== '') {
            $query->where([
                'OR' => [
                    'Medicines.code LIKE' => '%' . $q . '%',
                    'Medicines.code_kfa LIKE' => '%' . $q . '%',
                    'Medicines.name LIKE' => '%' . $q . '%',
                    'Medicines.generic_name LIKE' => '%' . $q . '%',
                    'Medicines.inventory_category LIKE' => '%' . $q . '%',
                ],
            ]);
        }

        $rows = $query->all();

        foreach ($rows as $m) {
            $results[] = [
                'id' => $m->id,
                'kode' => $m->code ?? '',
                'nama' => $m->name ?? '',
                'generic' => $m->generic_name ?? '',
                'kategori' => $m->inventory_category ?? '',
                'harga' => (float)($m->harga_jual ?? $m->hna ?? 0),
                'unit' => $m->unit_1 ?? '',
            ];
        }

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($results, JSON_UNESCAPED_UNICODE));
    }

    public function cariLaboratorium()
    {
        $this->request->allowMethod(['get']);
        $this->autoRender = false;

        $q = trim((string)$this->request->getQuery('q', ''));
        $category = trim((string)$this->request->getQuery('category', ''));
        $results = [];

        $query = $this->MasterLaboratories->find()
            ->where(['MasterLaboratories.is_active' => 1])
            ->order(['MasterLaboratories.name' => 'ASC'])
            ->limit($q === '' && $category === '' ? 300 : 100);

        if ($q !== '') {
            $query->where([
                'OR' => [
                    'MasterLaboratories.code LIKE' => '%' . $q . '%',
                    'MasterLaboratories.name LIKE' => '%' . $q . '%',
                    'MasterLaboratories.category LIKE' => '%' . $q . '%',
                ],
            ]);
        }

        if ($category !== '') {
            $query->where(['MasterLaboratories.category' => $category]);
        }

        $rows = $query->all();

        $schema = $this->MasterLaboratories->getSchema();
        $hasJasaRs = $schema->hasColumn('jasa_rs');
        $hasJasaDr = $schema->hasColumn('jasa_dr');
        $hasJasaPerawat = $schema->hasColumn('jasa_perawat');

        foreach ($rows as $lab) {
            $jasaRs = $hasJasaRs ? (float)($lab->jasa_rs ?? 0) : 0;
            $jasaDr = $hasJasaDr ? (float)($lab->jasa_dr ?? 0) : 0;
            $jasaPerawat = $hasJasaPerawat ? (float)($lab->jasa_perawat ?? 0) : 0;
            $tariff = (float)($lab->tariff ?? 0);

            if ($tariff <= 0) {
                $tariff = $jasaRs + $jasaDr + $jasaPerawat;
            }

            $results[] = [
                'id' => $lab->id,
                'kode' => $lab->code ?? '',
                'nama' => $lab->name ?? '',
                'tarif' => $tariff,
                'jasa_rs' => $jasaRs,
                'jasa_dr' => $jasaDr,
                'jasa_perawat' => $jasaPerawat,
                'kategori' => $lab->category ?? '',
            ];
        }

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($results, JSON_UNESCAPED_UNICODE));
    }

    public function kategoriLaboratorium()
    {
        $this->request->allowMethod(['get']);
        $this->autoRender = false;

        $rows = $this->MasterLaboratories->find()
            ->select(['category'])
            ->where([
                'MasterLaboratories.is_active' => 1,
                'MasterLaboratories.category IS NOT' => null,
                'MasterLaboratories.category !=' => '',
            ])
            ->distinct(['category'])
            ->order(['category' => 'ASC'])
            ->enableHydration(false)
            ->all()
            ->extract('category')
            ->toList();

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($rows, JSON_UNESCAPED_UNICODE));
    }

    public function cariRadiologi()
    {
        $this->request->allowMethod(['get']);
        $this->autoRender = false;

        $q = trim((string)$this->request->getQuery('q', ''));
        $results = [];

        if (strlen($q) >= 2) {
            $rows = $this->MasterRadiologies->find()
                ->where([
                    'OR' => [
                        'MasterRadiologies.code LIKE' => '%' . $q . '%',
                        'MasterRadiologies.name LIKE' => '%' . $q . '%',
                        'MasterRadiologies.category LIKE' => '%' . $q . '%',
                    ],
                    'MasterRadiologies.is_active' => 1,
                ])
                ->order(['MasterRadiologies.name' => 'ASC'])
                ->limit(30)
                ->all();

            foreach ($rows as $radio) {
                $results[] = [
                    'id' => $radio->id,
                    'kode' => $radio->code ?? '',
                    'nama' => $radio->name ?? '',
                    'tarif' => (float)($radio->tariff ?? 0),
                    'kategori' => $radio->category ?? '',
                    'modality' => $radio->modality ?? '',
                ];
            }
        }

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($results, JSON_UNESCAPED_UNICODE));
    }

    private function cleanMoney($value): float
    {
        $value = str_replace(['Rp', ' ', '.'], ['', '', ''], (string)$value);
        $value = str_replace(',', '.', $value);

        return (float)$value;
    }

    private function generateOrderNumber(): string
    {
        $prefix = 'ORD' . date('Ymd');

        $lastOrder = $this->Orders->find()
            ->select(['id'])
            ->order(['Orders.id' => 'DESC'])
            ->first();

        $next = $lastOrder ? ((int)$lastOrder->id + 1) : 1;

        return $prefix . str_pad((string)$next, 6, '0', STR_PAD_LEFT);
    }
}