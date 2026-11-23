<?php
declare(strict_types=1);

namespace App\Controller\Master;

use App\Controller\AppController;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * MasterTenagaMedis Controller
 *
 * @property \App\Model\Table\MasterTenagaMedisTable $MasterTenagaMedis
 * @method \App\Model\Entity\MasterTenagaMedi[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MasterTenagaMedisController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $q = trim((string)$this->request->getQuery('q'));
        $isActive = (string)$this->request->getQuery('is_active');

        $query = $this->MasterTenagaMedis->find();

        if ($q !== '') {
            $query->where([
                'OR' => [
                    'nama LIKE' => '%' . $q . '%',
                    'kode LIKE' => '%' . $q . '%',
                    'status LIKE' => '%' . $q . '%',
                ],
            ]);
        }

        if ($isActive !== '') {
            $query->where(['is_active' => (int)$isActive]);
        }

        $masterTenagaMedis = $this->paginate($query);

        // Daftar status/kategori untuk filter popup export
        $exportStatuses = [];
        $statusRows = $this->MasterTenagaMedis->find()
            ->select(['status'])
            ->distinct(['status'])
            ->where(['status IS NOT' => null, 'status !=' => ''])
            ->orderBy(['status' => 'ASC'])
            ->enableHydration(false)
            ->all();
        foreach ($statusRows as $r) {
            $exportStatuses[$r['status']] = $r['status'];
        }

        $this->set(compact('masterTenagaMedis', 'q', 'isActive', 'exportStatuses'));
    }

        /**
     * Export Excel
     */
    public function excel()
    {
        $this->request->allowMethod(['get']);

        $q         = trim((string)$this->request->getQuery('q'));
        $activeRaw = trim((string)$this->request->getQuery('is_active'));
        $statuses  = array_values(array_filter((array)$this->request->getQuery('status')));

        $query = $this->MasterTenagaMedis->find();

        if (!empty($statuses)) {
            $query->where(['status IN' => $statuses]);
        }
        if ($activeRaw === '1') {
            $query->where(['is_active' => 1]);
        } elseif ($activeRaw === '0') {
            $query->where(['is_active' => 0]);
        }
        if ($q !== '') {
            $query->where(['OR' => [
                'nama LIKE' => '%' . $q . '%',
                'kode LIKE' => '%' . $q . '%',
                'status LIKE' => '%' . $q . '%',
            ]]);
        }

        $query->orderBy(['status' => 'ASC', 'nama' => 'ASC']);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Tenaga Medis');

        $headers = ['No', 'Kode', 'Nama', 'Status', 'Aktif'];
        $lastCol = 'E';

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
            $sheet->setCellValueExplicit("B{$row}", (string)($item->kode ?? ''), DataType::TYPE_STRING);
            $sheet->setCellValue("C{$row}", (string)($item->nama ?? ''));
            $sheet->setCellValue("D{$row}", (string)($item->status ?? '-'));
            $sheet->setCellValue("E{$row}", !empty($item->is_active) ? 'Aktif' : 'Nonaktif');
            $row++;
        }

        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->freezePane('A2');

        $tmp = tempnam(sys_get_temp_dir(), 'tm_');
        (new Xlsx($spreadsheet))->save($tmp);
        $body = file_get_contents($tmp);
        unlink($tmp);

        return $this->response
            ->withType('xlsx')
            ->withDownload('tenaga_medis_' . date('Ymd_His') . '.xlsx')
            ->withStringBody($body);
    }
    /**
     * Toggle is_active via AJAX
     *
     * @param string|null $id
     * @return \Cake\Http\Response
     */
    public function toggleActive($id = null)
    {
        $this->request->allowMethod(['post']);
        $this->autoRender = false;

        try {
            $masterTenagaMedi = $this->MasterTenagaMedis->get($id);
            $masterTenagaMedi->is_active = $masterTenagaMedi->is_active ? 0 : 1;

            $saved = $this->MasterTenagaMedis->save($masterTenagaMedi);

            if (!$saved) {
                return $this->response
                    ->withStatus(422)
                    ->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => false,
                        'message' => 'Gagal menyimpan status aktif.',
                        'errors' => $masterTenagaMedi->getErrors(),
                    ]));
            }

            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => true,
                    'is_active' => (int)$masterTenagaMedi->is_active,
                ]));
        } catch (\Exception $e) {
            return $this->response
                ->withStatus(500)
                ->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => $e->getMessage(),
                ]));
        }
    }

    /**
     * View method
     *
     * @param string|null $id Master Tenaga Medi id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $masterTenagaMedi = $this->MasterTenagaMedis->get($id, contain: []);

        $this->set(compact('masterTenagaMedi'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $masterTenagaMedi = $this->MasterTenagaMedis->newEmptyEntity();
        if ($this->request->is('post')) {
            $masterTenagaMedi = $this->MasterTenagaMedis->patchEntity($masterTenagaMedi, $this->request->getData());
            if ($this->MasterTenagaMedis->save($masterTenagaMedi)) {
                $this->Flash->success(__('The master tenaga medi has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The master tenaga medi could not be saved. Please, try again.'));
        }
        $this->set(compact('masterTenagaMedi'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Master Tenaga Medi id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $masterTenagaMedi = $this->MasterTenagaMedis->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $masterTenagaMedi = $this->MasterTenagaMedis->patchEntity($masterTenagaMedi, $this->request->getData());
            if ($this->MasterTenagaMedis->save($masterTenagaMedi)) {
                $this->Flash->success(__('The master tenaga medi has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The master tenaga medi could not be saved. Please, try again.'));
        }
        $this->set(compact('masterTenagaMedi'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Master Tenaga Medi id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $masterTenagaMedi = $this->MasterTenagaMedis->get($id);
        if ($this->MasterTenagaMedis->delete($masterTenagaMedi)) {
            $this->Flash->success(__('The master tenaga medi has been deleted.'));
        } else {
            $this->Flash->error(__('The master tenaga medi could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Search for modal (AJAX, JSON)
     *
     * @return \Cake\Http\Response
     */
    public function searchModal()
    {
        $this->request->allowMethod(['get']);
        $this->autoRender = false;

        $keyword = trim((string)$this->request->getQuery('q'));
        $kategori = trim((string)$this->request->getQuery('kategori'));

        $query = $this->MasterTenagaMedis->find()
            ->select(['id', 'kode', 'nama', 'status'])
            ->orderBy(['nama' => 'ASC'])
            ->limit(50);

        if ($keyword !== '') {
            $query->where([
                'OR' => [
                    'nama LIKE' => '%' . $keyword . '%',
                    'kode LIKE' => '%' . $keyword . '%',
                ],
            ]);
        }

        if ($kategori !== '') {
            $query->where(['status LIKE' => '%' . $kategori . '%']);
        }

        $this->response = $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($query->toArray()));

        return $this->response;
    }

    /**
     * Generate kode otomatis berdasarkan kategori (AJAX, JSON)
     *
     * @return \Cake\Http\Response
     */
    public function nextKode()
    {
        $this->request->allowMethod(['get']);
        $this->autoRender = false;

        $kategori = strtoupper(trim((string)$this->request->getQuery('kategori')));

        $prefixMap = [
            'PERAWAT/SUSTER' => 'PWT',
            'BIDAN' => 'BDN',
            'ANALIS LABORATORIUM' => 'ANL',
            'PENUNJANG MEDIS' => 'PNJ',
            'RADIOGRAFER' => 'RAD',
            'FISIOTERAPIS' => 'FIS',
            'AHLI GIZI' => 'GZI',
            'APOTEKER' => 'APT',
            'TENAGA TEKNIS KEFARMASIAN' => 'TTK',
            'UMUM' => 'UMM',
        ];

        $prefix = $prefixMap[$kategori] ?? 'TMD';

        $lastRecord = $this->MasterTenagaMedis->find()
            ->select(['kode'])
            ->where(['kode LIKE' => $prefix . '-%'])
            ->orderBy(['id' => 'DESC'])
            ->first();

        $nextNumber = 1;
        if ($lastRecord && preg_match('/-(\d+)$/', $lastRecord->kode, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        }

        $newKode = $prefix . '-' . str_pad((string)$nextNumber, 5, '0', STR_PAD_LEFT);

        $this->response = $this->response
            ->withType('application/json')
            ->withStringBody(json_encode([
                'success' => true,
                'kode' => $newKode,
                'prefix' => $prefix,
                'next_no' => $nextNumber,
            ]));

        return $this->response;
    }
}
