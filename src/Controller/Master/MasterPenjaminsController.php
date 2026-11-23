<?php

declare(strict_types=1);

namespace App\Controller\Master;

use App\Controller\AppController;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * MasterPenjamins Controller
 *
 * @property \App\Model\Table\MasterPenjaminsTable $MasterPenjamins
 * @method \App\Model\Entity\MasterPenjamin[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MasterPenjaminsController extends AppController
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

        $query = $this->MasterPenjamins->find();

        if ($q !== '') {
            $query->where([
                'OR' => [
                    'MasterPenjamins.code LIKE' => '%' . $q . '%',
                    'MasterPenjamins.name LIKE' => '%' . $q . '%',
                    'MasterPenjamins.type LIKE' => '%' . $q . '%',
                ],
            ]);
        }

        if ($isActive !== '') {
            $query->where([
                'MasterPenjamins.is_active' => (int)$isActive,
            ]);
        }

        $this->paginate = [
            'limit' => 20,
            'order' => [
                'MasterPenjamins.id' => 'DESC',
            ],
        ];

        $masterPenjamins = $this->paginate($query);

        // Daftar jenis untuk filter popup export
        $exportTypes = [];
        $typeRows = $this->MasterPenjamins->find()
            ->select(['type'])
            ->distinct(['type'])
            ->where(['type IS NOT' => null, 'type !=' => ''])
            ->orderBy(['type' => 'ASC'])
            ->enableHydration(false)
            ->all();
        foreach ($typeRows as $r) {
            $exportTypes[$r['type']] = $r['type'];
        }

        $this->set(compact(
            'masterPenjamins',
            'q',
            'isActive',
            'exportTypes'
        ));
    }

    /**
     * View method
     *
     * @param string|null $id Master Penjamin id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $masterPenjamin = $this->MasterPenjamins->get($id);

        if ($this->request->is('ajax')) {

            $this->viewBuilder()->setClassName('Json');

            $this->set([
                'success' => true,
                'data' => $masterPenjamin,
                '_serialize' => ['success', 'data']
            ]);

            return;
        }

        $this->set(compact('masterPenjamin'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $masterPenjamin = $this->MasterPenjamins->newEmptyEntity();

        // Generate kode saat halaman pertama kali dibuka
        $masterPenjamin->code = $this->generatePenjaminCode();
        $masterPenjamin->is_active = 1;

        if ($this->request->is('post')) {

            $data = $this->request->getData();

            // Kalau code kosong, isi lagi
            if (empty($data['code'])) {
                $data['code'] = $this->generatePenjaminCode();
            }

            if (!isset($data['is_active']) || $data['is_active'] === '') {
                $data['is_active'] = 1;
            }

            $masterPenjamin = $this->MasterPenjamins->patchEntity(
                $masterPenjamin,
                $data
            );

            if ($this->MasterPenjamins->save($masterPenjamin)) {
                $this->Flash->success('Master penjamin berhasil disimpan.');
                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error('Master penjamin gagal disimpan.');
        }

        $this->set(compact('masterPenjamin'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Master Penjamin id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $masterPenjamin = $this->MasterPenjamins->get($id);

        if ($this->request->is(['patch', 'post', 'put'])) {

            $data = $this->request->getData();

            if (!isset($data['is_active']) || $data['is_active'] === '') {
                $data['is_active'] = 0;
            }

            $masterPenjamin = $this->MasterPenjamins->patchEntity(
                $masterPenjamin,
                $data
            );

            if ($this->MasterPenjamins->save($masterPenjamin)) {

                $this->Flash->success(
                    'Master penjamin berhasil diubah.'
                );

                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error(
                'Master penjamin gagal diubah.'
            );
        }

        $this->set(compact('masterPenjamin'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Master Penjamin id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        $masterPenjamin = $this->MasterPenjamins->get($id);

        $masterPenjamin->is_active = 0;

        if ($this->MasterPenjamins->save($masterPenjamin)) {

            $this->Flash->success(
                'Master penjamin berhasil dinonaktifkan.'
            );
        } else {

            $this->Flash->error(
                'Master penjamin gagal dinonaktifkan.'
            );
        }

        return $this->redirect(['action' => 'index']);
    }

        /**
     * Export Excel
     */
    public function excel()
    {
        $this->request->allowMethod(['get']);

        $q         = trim((string)$this->request->getQuery('q'));
        $activeRaw = trim((string)$this->request->getQuery('is_active'));
        $types     = array_values(array_filter((array)$this->request->getQuery('type')));

        $query = $this->MasterPenjamins->find();

        if (!empty($types)) {
            $query->where(['type IN' => $types]);
        }
        if ($activeRaw === '1' || $activeRaw === '0') {
            $query->where(['is_active' => (int)$activeRaw]);
        }
        if ($q !== '') {
            $query->where(['OR' => [
                'code LIKE' => '%' . $q . '%',
                'name LIKE' => '%' . $q . '%',
                'type LIKE' => '%' . $q . '%',
            ]]);
        }

        $query->orderBy(['type' => 'ASC', 'name' => 'ASC']);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Master Penjamin');

        $headers = ['No', 'Kode', 'Nama Penjamin', 'Jenis', 'Status', 'Diubah'];
        $lastCol = 'F';

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
            $sheet->setCellValue("D{$row}", (string)($item->type ?: '-'));
            $sheet->setCellValue("E{$row}", !empty($item->is_active) ? 'Aktif' : 'Nonaktif');
            $sheet->setCellValue("F{$row}", !empty($item->modified) ? $item->modified->format('d-m-Y H:i') : '-');
            $row++;
        }

        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->freezePane('A2');

        $tmp = tempnam(sys_get_temp_dir(), 'pjm_');
        (new Xlsx($spreadsheet))->save($tmp);
        $body = file_get_contents($tmp);
        unlink($tmp);

        return $this->response
            ->withType('xlsx')
            ->withDownload('master_penjamin_' . date('Ymd_His') . '.xlsx')
            ->withStringBody($body);
    }

    public function toggleActive($id = null)
    {
        $this->request->allowMethod(['post']);

        $masterPenjamin = $this->MasterPenjamins->get($id);

        $masterPenjamin->is_active = !$masterPenjamin->is_active;

        $success = $this->MasterPenjamins->save($masterPenjamin);

        $this->viewBuilder()->setClassName('Json');

        $this->set([
            'success' => (bool)$success,
            'is_active' => (int)$masterPenjamin->is_active,
            '_serialize' => ['success', 'is_active']
        ]);
    }

    private function generatePenjaminCode(): string
    {
        $last = $this->MasterPenjamins
            ->find()
            ->select(['id'])
            ->orderBy([
                'MasterPenjamins.id' => 'DESC'
            ])
            ->first();

        $next = $last
            ? ((int)$last->id + 1)
            : 1;

        return 'PJM-' . str_pad(
            (string)$next,
            4,
            '0',
            STR_PAD_LEFT
        );
    }
}
