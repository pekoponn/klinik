<?php
declare(strict_types=1);

namespace App\Controller\Master;

use App\Controller\AppController;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Insurances Controller
 *
 * @property \App\Model\Table\InsurancesTable $Insurances
 * @method \App\Model\Entity\Insurance[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class InsurancesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $filterCode = trim((string)$this->request->getQuery('filterCode'));
        $filterName = trim((string)$this->request->getQuery('filterName'));

        $query = $this->Insurances->find();

        if ($filterCode !== '') {
            $query->where(['Insurances.code LIKE' => '%' . $filterCode . '%']);
        }
        if ($filterName !== '') {
            $query->where(['Insurances.name LIKE' => '%' . $filterName . '%']);
        }

        $insurances = $this->paginate($query);

        // Daftar region & tipe untuk filter popup export
        $exportRegions = [];
        $regionRows = $this->Insurances->find()
            ->select(['region'])
            ->distinct(['region'])
            ->where(['region IS NOT' => null, 'region !=' => ''])
            ->orderBy(['region' => 'ASC'])
            ->enableHydration(false)
            ->all();
        foreach ($regionRows as $r) {
            $exportRegions[$r['region']] = $r['region'];
        }

        $exportTypes = [];
        $typeRows = $this->Insurances->find()
            ->select(['type'])
            ->distinct(['type'])
            ->where(['type IS NOT' => null, 'type !=' => ''])
            ->orderBy(['type' => 'ASC'])
            ->enableHydration(false)
            ->all();
        foreach ($typeRows as $r) {
            $exportTypes[$r['type']] = $r['type'];
        }

        $this->set(compact('insurances', 'exportRegions', 'exportTypes', 'filterCode', 'filterName'));
    }

    public function excel()
    {
        $this->request->allowMethod(['get']);

        $q         = trim((string)$this->request->getQuery('q'));
        $activeRaw = trim((string)$this->request->getQuery('is_active'));
        $regions   = array_values(array_filter((array)$this->request->getQuery('region')));
        $types     = array_values(array_filter((array)$this->request->getQuery('type')));

        $query = $this->Insurances->find();

        if (!empty($regions)) {
            $query->where(['region IN' => $regions]);
        }
        if (!empty($types)) {
            $query->where(['type IN' => $types]);
        }
        if ($activeRaw === 'Y' || $activeRaw === 'N') {
            $query->where(['is_active' => $activeRaw]);
        }
        if ($q !== '') {
            $query->where(['OR' => [
                'code LIKE' => '%' . $q . '%',
                'region LIKE' => '%' . $q . '%',
                'name LIKE' => '%' . $q . '%',
                'type LIKE' => '%' . $q . '%',
                'bank_name LIKE' => '%' . $q . '%',
                'bank_account_number LIKE' => '%' . $q . '%',
                'bank_account_name LIKE' => '%' . $q . '%',
                'phone_number_rs LIKE' => '%' . $q . '%',
                'email_rs LIKE' => '%' . $q . '%',
            ]]);
        }

        $query->orderBy(['type' => 'ASC', 'name' => 'ASC']);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Insurance');

        $headers = ['No', 'Kode', 'Region', 'Nama', 'Tipe', 'Bank', 'No Rekening', 'Atas Nama', 'Phone RS', 'Email RS', 'Status'];
        $lastCol = 'K';

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
            $sheet->setCellValue("C{$row}", (string)($item->region ?: '-'));
            $sheet->setCellValue("D{$row}", (string)($item->name ?? ''));
            $sheet->setCellValue("E{$row}", (string)($item->type ?: '-'));
            $sheet->setCellValue("F{$row}", (string)($item->bank_name ?: '-'));
            $sheet->setCellValueExplicit("G{$row}", (string)($item->bank_account_number ?? ''), DataType::TYPE_STRING);
            $sheet->setCellValue("H{$row}", (string)($item->bank_account_name ?: '-'));
            $sheet->setCellValueExplicit("I{$row}", (string)($item->phone_number_rs ?? ''), DataType::TYPE_STRING);
            $sheet->setCellValue("J{$row}", (string)($item->email_rs ?: '-'));
            $sheet->setCellValue("K{$row}", $item->is_active === 'Y' ? 'Aktif' : 'Nonaktif');
            $row++;
        }

        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->freezePane('A2');

        $tmp = tempnam(sys_get_temp_dir(), 'ins_');
        (new Xlsx($spreadsheet))->save($tmp);
        $body = file_get_contents($tmp);
        unlink($tmp);

        return $this->response
            ->withType('xlsx')
            ->withDownload('insurance_' . date('Ymd_His') . '.xlsx')
            ->withStringBody($body);
    }

    /**
     * View method
     *
     * @param string|null $id Insurance id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $insurance = $this->Insurances->get($id, contain: []);

        $this->set(compact('insurance'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $insurance = $this->Insurances->newEmptyEntity();
        if ($this->request->is('post')) {
            $insurance = $this->Insurances->patchEntity($insurance, $this->request->getData());
            if ($this->Insurances->save($insurance)) {
                $this->Flash->success(__('The insurance has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The insurance could not be saved. Please, try again.'));
        }
        $this->set(compact('insurance'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Insurance id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $insurance = $this->Insurances->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $insurance = $this->Insurances->patchEntity($insurance, $this->request->getData());
            if ($this->Insurances->save($insurance)) {
                $this->Flash->success(__('The insurance has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The insurance could not be saved. Please, try again.'));
        }
        $this->set(compact('insurance'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Insurance id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $insurance = $this->Insurances->get($id);
        if ($this->Insurances->delete($insurance)) {
            $this->Flash->success(__('The insurance has been deleted.'));
        } else {
            $this->Flash->error(__('The insurance could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
