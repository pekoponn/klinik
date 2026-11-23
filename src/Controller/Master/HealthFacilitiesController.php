<?php
declare(strict_types=1);

namespace App\Controller\Master;

use App\Controller\AppController;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * HealthFacilities Controller
 *
 * @property \App\Model\Table\HealthFacilitiesTable $HealthFacilities
 * @method \App\Model\Entity\HealthFacility[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class HealthFacilitiesController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();

        $this->HealthFacilities = $this->fetchTable('HealthFacilities');
    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $filterCode = trim((string)$this->request->getQuery('filterCode'));
        $filterName = trim((string)$this->request->getQuery('filterName'));

        $query = $this->HealthFacilities->find();

        if ($filterCode !== '') {
            $query->where(['HealthFacilities.code LIKE' => '%' . $filterCode . '%']);
        }
        if ($filterName !== '') {
            $query->where(['HealthFacilities.name LIKE' => '%' . $filterName . '%']);
        }

        $healthFacilities = $this->paginate($query);

        $exportTypes = [];
        $typeRows = $this->HealthFacilities->find()
            ->select(['type'])
            ->distinct(['type'])
            ->where(['type IS NOT' => null, 'type !=' => ''])
            ->orderBy(['type' => 'ASC'])
            ->enableHydration(false)
            ->all();
        foreach ($typeRows as $r) {
            $exportTypes[$r['type']] = $r['type'];
        }

        $this->set(compact('healthFacilities', 'exportTypes', 'filterCode', 'filterName'));
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

        $query = $this->HealthFacilities->find();

        if (!empty($types)) {
            $query->where(['type IN' => $types]);
        }
        if ($activeRaw === 'Y' || $activeRaw === 'N') {
            $query->where(['is_active' => $activeRaw]);
        }
        if ($q !== '') {
            $query->where(['OR' => [
                'code LIKE' => '%' . $q . '%',
                'code_int LIKE' => '%' . $q . '%',
                'name LIKE' => '%' . $q . '%',
                'type LIKE' => '%' . $q . '%',
                'code_ppk LIKE' => '%' . $q . '%',
            ]]);
        }

        $query->orderBy(['type' => 'ASC', 'name' => 'ASC']);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Health Facility');

        $headers = ['No', 'Kode', 'Kode Internal', 'Nama', 'Type', 'Code PPK', 'Status'];
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
            $sheet->setCellValueExplicit("C{$row}", (string)($item->code_int ?? ''), DataType::TYPE_STRING);
            $sheet->setCellValue("D{$row}", (string)($item->name ?? ''));
            $sheet->setCellValue("E{$row}", (string)($item->type ?: '-'));
            $sheet->setCellValueExplicit("F{$row}", (string)($item->code_ppk ?? ''), DataType::TYPE_STRING);
            $sheet->setCellValue("G{$row}", $item->is_active === 'Y' ? 'Aktif' : 'Nonaktif');
            $row++;
        }

        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->freezePane('A2');

        $tmp = tempnam(sys_get_temp_dir(), 'hf_');
        (new Xlsx($spreadsheet))->save($tmp);
        $body = file_get_contents($tmp);
        unlink($tmp);

        return $this->response
            ->withType('xlsx')
            ->withDownload('health_facility_' . date('Ymd_His') . '.xlsx')
            ->withStringBody($body);
    }

    /**
     * View method
     *
     * @param string|null $id Health Facility id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $healthFacility = $this->HealthFacilities->get($id, contain: []);

        $this->set(compact('healthFacility'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $healthFacility = $this->HealthFacilities->newEmptyEntity();
        if ($this->request->is('post')) {
            $healthFacility = $this->HealthFacilities->patchEntity($healthFacility, $this->request->getData());
            if ($this->HealthFacilities->save($healthFacility)) {
                $this->Flash->success(__('The health facility has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The health facility could not be saved. Please, try again.'));
        }
        $this->set(compact('healthFacility'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Health Facility id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $healthFacility = $this->HealthFacilities->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $healthFacility = $this->HealthFacilities->patchEntity($healthFacility, $this->request->getData());
            if ($this->HealthFacilities->save($healthFacility)) {
                $this->Flash->success(__('The health facility has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The health facility could not be saved. Please, try again.'));
        }
        $this->set(compact('healthFacility'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Health Facility id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $healthFacility = $this->HealthFacilities->get($id);
        if ($this->HealthFacilities->delete($healthFacility)) {
            $this->Flash->success(__('The health facility has been deleted.'));
        } else {
            $this->Flash->error(__('The health facility could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
