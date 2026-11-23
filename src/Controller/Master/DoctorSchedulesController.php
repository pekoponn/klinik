<?php
declare(strict_types=1);

namespace App\Controller\Master;

use App\Controller\AppController;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DoctorSchedulesController extends AppController
{
        public function index()
    {
        $dayOrderCase = "CASE DoctorSchedules.day
            WHEN 'Senin' THEN 1
            WHEN 'Selasa' THEN 2
            WHEN 'Rabu' THEN 3
            WHEN 'Kamis' THEN 4
            WHEN 'Jumat' THEN 5
            WHEN 'Sabtu' THEN 6
            WHEN 'Minggu' THEN 7
            ELSE 8
        END";

        $query = $this->DoctorSchedules->find()
            ->contain(['Doctors'])
             ->select([
                'DoctorSchedules.id',
                'DoctorSchedules.code',
                'DoctorSchedules.doctor_id',
                'DoctorSchedules.day',
                'DoctorSchedules.start_time',
                'DoctorSchedules.end_time',
                'DoctorSchedules.quota',
                'DoctorSchedules.quota_used',
                'DoctorSchedules.estimated_minutes',
                'DoctorSchedules.note',
                'DoctorSchedules.is_active',
                'Doctors.id',
                'Doctors.doctor_name',
                'Doctors.polyclinic_name',
                'day_order' => $dayOrderCase,
            ])
            ->order([
                'Doctors.doctor_name' => 'ASC',
                'day_order' => 'ASC',
            ]);
        
        $q = trim((string)$this->request->getQuery('q'));
        $dayFilter = $this->request->getQuery('day');
        $activeFilter = $this->request->getQuery('is_active');

        if ($q !== '') {
            $query->andWhere(['OR' => [
                'DoctorSchedules.code LIKE' => '%' . $q . '%',
                'Doctors.doctor_name LIKE' => '%' . $q . '%',
                'Doctors.polyclinic_name LIKE' => '%' . $q . '%',
            ]]);
        }

        if ($dayFilter !== null && $dayFilter !== '') {
            $query->andWhere(['DoctorSchedules.day' => $dayFilter]);
        }

        if ($activeFilter !== null && $activeFilter !== '') {
            if ($activeFilter === '1') {
                $query->andWhere(['DoctorSchedules.is_active IN' => [1, '1', 'Y', true]]);
            } else {
                $query->andWhere(['DoctorSchedules.is_active IN' => [0, '0', 'N', false]]);
            }
        }

        $this->paginate = [
            'limit' => 200,
            'order' => [],
        ];

        $doctorSchedules = $this->paginate($query);

        foreach ($doctorSchedules as $ds) {
            $ds->effective_quota_used = $this->getEffectiveQuotaUsed($ds);
        }

        // Daftar dokter dan poli untuk filter popup export
        $exportDoctors = $this->DoctorSchedules->Doctors->find('list', [
            'keyField' => 'id',
            'valueField' => 'doctor_name',
        ])->orderBy(['Doctors.doctor_name' => 'ASC'])->toArray();

        $exportPolys = [];
        $polyRows = $this->DoctorSchedules->Doctors->find()
            ->select(['polyclinic_name'])
            ->distinct(['polyclinic_name'])
            ->where(['Doctors.polyclinic_name IS NOT' => null, 'Doctors.polyclinic_name !=' => ''])
            ->orderBy(['Doctors.polyclinic_name' => 'ASC'])
            ->enableHydration(false)
            ->all();
        foreach ($polyRows as $r) {
            $exportPolys[$r['polyclinic_name']] = $r['polyclinic_name'];
        }

        $this->set(compact('doctorSchedules', 'exportDoctors', 'exportPolys'));
    }

        public function excel()
    {
        $this->request->allowMethod(['get']);

        $q         = trim((string)$this->request->getQuery('q'));
        $activeRaw = trim((string)$this->request->getQuery('is_active'));
        $doctorIds = array_values(array_filter((array)$this->request->getQuery('doctor_id')));
        $polys     = array_values(array_filter((array)$this->request->getQuery('polyclinic')));
        $days      = array_values(array_filter((array)$this->request->getQuery('day')));

        $dayOrderCase = "CASE DoctorSchedules.day
            WHEN 'Senin' THEN 1
            WHEN 'Selasa' THEN 2
            WHEN 'Rabu' THEN 3
            WHEN 'Kamis' THEN 4
            WHEN 'Jumat' THEN 5
            WHEN 'Sabtu' THEN 6
            WHEN 'Minggu' THEN 7
            ELSE 8
        END";

        $query = $this->DoctorSchedules->find()
            ->contain(['Doctors'])
            ->select($this->DoctorSchedules)
            ->select(['Doctors.id', 'Doctors.doctor_name', 'Doctors.polyclinic_name', 'Doctors.specialist'])
            ->select(['day_order' => $dayOrderCase]);

        if (!empty($doctorIds)) {
            $query->where(['DoctorSchedules.doctor_id IN' => array_map('intval', $doctorIds)]);
        }
        if (!empty($polys)) {
            $query->where(['Doctors.polyclinic_name IN' => $polys]);
        }
        if (!empty($days)) {
            $query->where(['DoctorSchedules.day IN' => $days]);
        }
        if ($activeRaw === '1') {
            $query->where(['DoctorSchedules.is_active IN' => [1, '1', 'Y', true]]);
        } elseif ($activeRaw === '0') {
            $query->where(['DoctorSchedules.is_active IN' => [0, '0', 'N', false]]);
        }
        if ($q !== '') {
            $query->where(['OR' => [
                'DoctorSchedules.code LIKE' => '%' . $q . '%',
                'Doctors.doctor_name LIKE' => '%' . $q . '%',
                'Doctors.polyclinic_name LIKE' => '%' . $q . '%',
            ]]);
        }

        $query->orderBy([
            'Doctors.polyclinic_name' => 'ASC',
            'Doctors.doctor_name' => 'ASC',
            'day_order' => 'ASC',
        ]);

        $fmtTime = static function ($v): string {
            if (empty($v)) {
                return '-';
            }
            if (is_object($v) && method_exists($v, 'format')) {
                return $v->format('H:i');
            }

            return substr((string)$v, 0, 5);
        };

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Jadwal Dokter');

        $headers = [
            'No', 'Kode Jadwal', 'Dokter', 'Spesialis', 'Poliklinik', 'Hari',
            'Jam Mulai', 'Jam Selesai', 'Kuota', 'Terpakai', 'Sisa Kuota',
            'Estimasi (menit/pasien)', 'Catatan', 'Status',
        ];
        $lastCol = 'N';

        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '111827']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $row = 2;
        $no = 1;
        foreach ($query as $ds) {
            $isActive = in_array($ds->is_active ?? null, ['Y', 1, '1', true], true);
            $quota = (int)($ds->quota ?? 0);
            $used = (int)$this->getEffectiveQuotaUsed($ds);

            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValueExplicit("B{$row}", (string)($ds->code ?? ''), DataType::TYPE_STRING);
            $sheet->setCellValue("C{$row}", (string)($ds->doctor->doctor_name ?? '-'));
            $sheet->setCellValue("D{$row}", (string)($ds->doctor->specialist ?? ''));
            $sheet->setCellValue("E{$row}", (string)($ds->doctor->polyclinic_name ?? '-'));
            $sheet->setCellValue("F{$row}", (string)($ds->day ?? ''));
            $sheet->setCellValue("G{$row}", $fmtTime($ds->start_time ?? null));
            $sheet->setCellValue("H{$row}", $fmtTime($ds->end_time ?? null));
            $sheet->setCellValue("I{$row}", $quota);
            $sheet->setCellValue("J{$row}", $used);
            $sheet->setCellValue("K{$row}", max(0, $quota - $used));
            $sheet->setCellValue("L{$row}", !empty($ds->estimated_minutes) ? (int)$ds->estimated_minutes : '-');
            $sheet->setCellValue("M{$row}", (string)($ds->note ?? ''));
            $sheet->setCellValue("N{$row}", $isActive ? 'Aktif' : 'Tidak Aktif');
            $row++;
        }

        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->freezePane('A2');

        $tmp = tempnam(sys_get_temp_dir(), 'ds_');
        (new Xlsx($spreadsheet))->save($tmp);
        $body = file_get_contents($tmp);
        unlink($tmp);

        return $this->response
            ->withType('xlsx')
            ->withDownload('jadwal_dokter_' . date('Ymd_His') . '.xlsx')
            ->withStringBody($body);
    }

        public function view($id = null)
    {
        $doctorSchedule = $this->DoctorSchedules->get($id, [
            'contain' => ['Doctors'],
        ]);

        $this->archiveExpiredDoctorScheduleCycle($doctorSchedule);

        $currentCycleDate = $this->getDoctorScheduleCycleDate((string)$doctorSchedule->day);
        $currentQuotaUsed = (int)($doctorSchedule->quota_used ?? 0);

        $this->DoctorScheduleQuotaHistories = $this->fetchTable('DoctorScheduleQuotaHistories');
        $quotaHistories = $this->DoctorScheduleQuotaHistories->find()
            ->where(['doctor_schedule_id' => $doctorSchedule->id])
            ->order(['cycle_date' => 'DESC'])
            ->limit(30)
            ->all();

        $this->set(compact('doctorSchedule', 'currentCycleDate', 'currentQuotaUsed', 'quotaHistories'));
    }

       /**
     * Urutan hari dalam seminggu, dipakai untuk loop checkbox di form
     * dan untuk validasi nama hari yang dikirim dari form.
     */
    private function getWeekDayOrder(): array
    {
        return ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
    }

    private function calculateEstimatedMinutes(string $startTime, string $endTime, int $quota): ?int
    {
        if ($quota <= 0) {
            return null;
        }

        $start = strtotime($startTime);
        $end = strtotime($endTime);

        if ($start === false || $end === false || $end <= $start) {
            return null;
        }

        $totalMinutes = ($end - $start) / 60;

        return (int) round($totalMinutes / $quota);
    }

    public function add()
    {
        $doctorSchedule = $this->DoctorSchedules->newEmptyEntity();
        $weekDays = $this->getWeekDayOrder();

        if ($this->request->is('post')) {

            $data = $this->request->getData();

            $doctorId = $data['doctor_id'] ?? null;
            $isActive = $data['is_active'] ?? 'Y';
            $note = $data['note'] ?? null;
            $daysData = $data['days'] ?? [];

            if (empty($doctorId)) {
                $this->Flash->error(__('Dokter belum dipilih.'));

                return $this->redirect(['action' => 'add']);
            }

            // Kumpulkan hari yang dicentang beserta jam & kuotanya.
            $selectedDays = [];
            foreach ($weekDays as $dayName) {
                $dayRow = $daysData[$dayName] ?? null;

                if (empty($dayRow['checked'])) {
                    continue;
                }

                $startTime = trim((string)($dayRow['start_time'] ?? ''));
                $endTime = trim((string)($dayRow['end_time'] ?? ''));

                if ($startTime === '' || $endTime === '') {
                    $this->Flash->error(__('Jam mulai & jam selesai wajib diisi untuk hari ' . $dayName . '.'));

                    return $this->redirect(['action' => 'add']);
                }

                $selectedDays[] = [
                    'day' => $dayName,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'quota' => (int)($dayRow['quota'] ?? 0),
                ];
            }

            if (empty($selectedDays)) {
                $this->Flash->error(__('Pilih minimal satu hari jadwal praktik.'));

                return $this->redirect(['action' => 'add']);
            }

            $connection = $this->DoctorSchedules->getConnection();
            $connection->begin();

            $savedCount = 0;
            $saveErrors = [];

            foreach ($selectedDays as $index => $row) {
                $entity = $this->DoctorSchedules->newEmptyEntity();

                $entityData = [
                    'doctor_id' => $doctorId,
                    'day' => $row['day'],
                    'start_time' => $row['start_time'],
                    'end_time' => $row['end_time'],
                    'quota' => $row['quota'],
                    'estimated_minutes' => $this->calculateEstimatedMinutes(
                        $row['start_time'],
                        $row['end_time'],
                        $row['quota']
                    ),
                    'note' => $note,
                    'is_active' => $isActive,
                    'code' => 'DS-' . date('YmdHis') . '-' . strtoupper(substr($row['day'], 0, 3)),
                    'is_create' => 1,
                    'is_update' => 0,
                    'is_delete' => 0,
                    'delete_log' => null,
                ];

                $entity = $this->DoctorSchedules->patchEntity($entity, $entityData);

                if ($this->DoctorSchedules->save($entity)) {
                    $savedCount++;
                } else {
                    $saveErrors[$row['day']] = $entity->getErrors();
                }
            }

            if (!empty($saveErrors)) {
                $connection->rollback();

                $this->Flash->error(__('Sebagian jadwal gagal disimpan: ' . json_encode($saveErrors, JSON_PRETTY_PRINT)));

                return $this->redirect(['action' => 'add']);
            }

            $connection->commit();

            $this->Flash->success(__($savedCount . ' jadwal dokter berhasil disimpan untuk 1 minggu.'));

            return $this->redirect(['action' => 'index']);
        }

        // tampilkan nama dokter
        $doctors = $this->DoctorSchedules->Doctors
            ->find('list', [
                'keyField' => 'id',
                'valueField' => 'doctor_name' // ganti jika nama field berbeda
            ])
            ->toArray();

        // mapping doctor_id => nama poli, dipakai JS untuk auto-isi field Poli
        $doctorPolyclinics = $this->DoctorSchedules->Doctors
            ->find()
            ->select(['id', 'polyclinic_name'])
            ->all()
            ->combine('id', 'polyclinic_name')
            ->toArray();

        $this->set(compact(
            'doctorSchedule',
            'doctors',
            'doctorPolyclinics',
            'weekDays'
        ));
    }

    public function edit($id = null)
    {
        $doctorSchedule = $this->DoctorSchedules->get($id);

        if ($this->request->is([
            'patch',
            'post',
            'put'
        ])) {

            $data = $this->request->getData();

        $data['is_update'] = 1;

        if (!empty($data['start_time']) && !empty($data['end_time']) && isset($data['quota'])) {
            $data['estimated_minutes'] = $this->calculateEstimatedMinutes(
                (string)$data['start_time'],
                (string)$data['end_time'],
                (int)$data['quota']
            );
        }

        $doctorSchedule = $this->DoctorSchedules->patchEntity(
            $doctorSchedule,
            $data
        );

            if ($this->DoctorSchedules->save($doctorSchedule)) {

                $this->Flash->success(
                    __('Jadwal dokter berhasil diubah.')
                );

                return $this->redirect([
                    'action' => 'index'
                ]);
            }

            $errors = json_encode(
                $doctorSchedule->getErrors(),
                JSON_PRETTY_PRINT
            );

            $this->Flash->error(
                __('Jadwal dokter gagal diubah: ' . $errors)
            );
        }

        $doctors = $this->DoctorSchedules->Doctors
            ->find('list', [
                'keyField' => 'id',
                'valueField' => 'doctor_name' // ganti jika berbeda
            ])
            ->toArray();

        $this->set(compact(
            'doctorSchedule',
            'doctors'
        ));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod([
            'post',
            'delete'
        ]);

        $doctorSchedule = $this->DoctorSchedules->get($id);

        // soft delete
        $doctorSchedule->is_delete = 1;
        $doctorSchedule->delete_log = date('Y-m-d H:i:s');

        if ($this->DoctorSchedules->save($doctorSchedule)) {

            $this->Flash->success(
                __('Jadwal dokter berhasil dihapus.')
            );

        } else {

            $this->Flash->error(
                __('Jadwal dokter gagal dihapus.')
            );
        }

        return $this->redirect([
            'action' => 'index'
        ]);
    }
}