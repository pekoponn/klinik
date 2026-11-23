<?php
declare(strict_types=1);

namespace App\Controller\Master;

use App\Controller\AppController;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * MasterPatientReferrers Controller
 *
 * @property \App\Model\Table\MasterPatientReferrersTable $MasterPatientReferrers
 * @method \App\Model\Entity\MasterPatientReferrer[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MasterPatientReferrersController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $q = trim((string)$this->request->getQuery('q', ''));
        $isActive = $this->request->getQuery('is_active', '');

        $query = $this->MasterPatientReferrers->find();

        if ($q !== '') {
            $query->where([
                'OR' => [
                    'MasterPatientReferrers.code LIKE' => '%' . $q . '%',
                    'MasterPatientReferrers.name LIKE' => '%' . $q . '%',
                    'MasterPatientReferrers.referral_origin LIKE' => '%' . $q . '%',
                    'MasterPatientReferrers.doctor_sender LIKE' => '%' . $q . '%',
                ],
            ]);
        }

        if ($isActive !== '') {
            $query->where([
                'MasterPatientReferrers.is_active' => $isActive,
            ]);
        }

        $this->paginate = [
            'limit' => 20,
            'order' => [
                'MasterPatientReferrers.id' => 'DESC',
            ],
        ];
        $masterPatientReferrers = $this->paginate($query);
        $exportOrigins = [];
        $originRows = $this->MasterPatientReferrers->find()
            ->select(['referral_origin'])
            ->distinct(['referral_origin'])
            ->where(['referral_origin IS NOT' => null, 'referral_origin !=' => ''])
            ->orderBy(['referral_origin' => 'ASC'])
            ->enableHydration(false)
            ->all();
        foreach ($originRows as $r) {
            $exportOrigins[$r['referral_origin']] = $r['referral_origin'];
        }

        $this->set(compact(
            'masterPatientReferrers',
            'q',
            'isActive',
            'exportOrigins'
        ));
    }

    /**
     * View method
     *
     * @param string|null $id Master Patient Referrer id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $masterPatientReferrer = $this->MasterPatientReferrers->get($id);

        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setClassName('Json');

            $this->set([
                'success' => true,
                'data' => $masterPatientReferrer,
                '_serialize' => ['success', 'data'],
            ]);

            return;
        }

        $this->set(compact('masterPatientReferrer'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $masterPatientReferrer = $this->MasterPatientReferrers->newEmptyEntity();

        $masterPatientReferrer->code = $this->generateReferrerCode();
        $masterPatientReferrer->is_active = 'Y';

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            if (empty($data['code'])) {
                $data['code'] = $this->generateReferrerCode();
            }

            if (!isset($data['is_active']) || $data['is_active'] === '') {
                $data['is_active'] = 'Y';
            }

            $masterPatientReferrer = $this->MasterPatientReferrers->patchEntity(
                $masterPatientReferrer,
                $data
            );

            if ($this->MasterPatientReferrers->save($masterPatientReferrer)) {
                $this->Flash->success('Master perujuk pasien berhasil disimpan.');

                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error('Master perujuk pasien gagal disimpan.');
        }

        $this->set(compact('masterPatientReferrer'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Master Patient Referrer id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $masterPatientReferrer = $this->MasterPatientReferrers->get($id);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();

            if (!isset($data['is_active']) || $data['is_active'] === '') {
                $data['is_active'] = 'N';
            }

            $masterPatientReferrer = $this->MasterPatientReferrers->patchEntity(
                $masterPatientReferrer,
                $data
            );

            if ($this->MasterPatientReferrers->save($masterPatientReferrer)) {
                $this->Flash->success('Master perujuk pasien berhasil diubah.');

                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error('Master perujuk pasien gagal diubah.');
        }

        $this->set(compact('masterPatientReferrer'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Master Patient Referrer id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        $masterPatientReferrer = $this->MasterPatientReferrers->get($id);
        $masterPatientReferrer->is_active = 'N';

        if ($this->MasterPatientReferrers->save($masterPatientReferrer)) {
            $this->Flash->success('Master perujuk pasien berhasil dinonaktifkan.');
        } else {
            $this->Flash->error('Master perujuk pasien gagal dinonaktifkan.');
        }

        return $this->redirect(['action' => 'index']);
    }

    public function excel()
    {
        $this->request->allowMethod(['get']);

        $q         = trim((string)$this->request->getQuery('q'));
        $activeRaw = trim((string)$this->request->getQuery('is_active'));
        $origins   = array_values(array_filter((array)$this->request->getQuery('origin')));

        $query = $this->MasterPatientReferrers->find();

        if (!empty($origins)) {
            $query->where(['referral_origin IN' => $origins]);
        }
        if ($activeRaw === 'Y' || $activeRaw === 'N') {
            $query->where(['is_active' => $activeRaw]);
        }
        if ($q !== '') {
            $query->where(['OR' => [
                'code LIKE' => '%' . $q . '%',
                'name LIKE' => '%' . $q . '%',
                'referral_origin LIKE' => '%' . $q . '%',
                'doctor_sender LIKE' => '%' . $q . '%',
            ]]);
        }

        $query->orderBy(['referral_origin' => 'ASC', 'name' => 'ASC']);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Perujuk Pasien');

        $headers = ['No', 'Kode', 'Nama Perujuk', 'Asal Rujuk', 'Dokter Pengirim', 'Status', 'Diubah'];
        $lastCol = 'G';

        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $row = 2;
        $no = 1;
        foreach ($query as $item) {
            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValueExplicit("B{$row}", (string)($item->code ?? ''), DataType::TYPE_STRING);
            $sheet->setCellValue("C{$row}", (string)($item->name ?? ''));
            $sheet->setCellValue("D{$row}", (string)($item->referral_origin ?: '-'));
            $sheet->setCellValue("E{$row}", (string)($item->doctor_sender ?: '-'));
            $sheet->setCellValue("F{$row}", $item->is_active === 'Y' ? 'Aktif' : 'Nonaktif');
            $sheet->setCellValue("G{$row}", !empty($item->modified) ? $item->modified->format('d-m-Y H:i') : '-');
            $row++;
        }

        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->freezePane('A2');

        $tmp = tempnam(sys_get_temp_dir(), 'prj_');
        (new Xlsx($spreadsheet))->save($tmp);
        $body = file_get_contents($tmp);
        unlink($tmp);

        return $this->response
            ->withType('xlsx')
            ->withDownload('perujuk_pasien_' . date('Ymd_His') . '.xlsx')
            ->withStringBody($body);
    }

    public function toggleActive($id = null)
    {
        $this->request->allowMethod(['post']);

        $masterPatientReferrer = $this->MasterPatientReferrers->get($id);

        $masterPatientReferrer->is_active = $masterPatientReferrer->is_active === 'Y'
            ? 'N'
            : 'Y';

        $success = $this->MasterPatientReferrers->save($masterPatientReferrer);

        $this->viewBuilder()->setClassName('Json');

        $this->set([
            'success' => (bool)$success,
            'is_active' => $masterPatientReferrer->is_active,
            '_serialize' => ['success', 'is_active'],
        ]);
    }

    private function generateReferrerCode(): string
    {
        $last = $this->MasterPatientReferrers
            ->find()
            ->select(['id'])
            ->orderBy([
                'MasterPatientReferrers.id' => 'DESC',
            ])
            ->first();

        $next = $last
            ? ((int)$last->id + 1)
            : 1;

        return 'PRJ-' . str_pad(
            (string)$next,
            4,
            '0',
            STR_PAD_LEFT
        );
    }
}
