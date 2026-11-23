<?php
declare(strict_types=1);

namespace App\Controller\Master;

use App\Controller\AppController;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * PharmacyItems Controller
 *
 * @property \App\Model\Table\PharmacyItemsTable $PharmacyItems
 * @method \App\Model\Entity\PharmacyItem[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PharmacyItemsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->PharmacyItems->find()
            ->where(['PharmacyItems.is_delete' => 0])
            ->orderBy(['PharmacyItems.name' => 'ASC']);

        $code = trim((string)$this->request->getQuery('filterCode'));
        $name = trim((string)$this->request->getQuery('filterName'));
        $type = trim((string)$this->request->getQuery('filterType'));

        if ($code !== '') {
            $query->where(['PharmacyItems.code LIKE' => "%$code%"]);
        }
        if ($name !== '') {
            $query->where(['PharmacyItems.name LIKE' => "%$name%"]);
        }
        if ($type !== '') {
            $query->where(['PharmacyItems.type LIKE' => "%$type%"]);
        }

        $pharmacyItems = $this->paginate($query);
        $distinct = function (string $field): array {
            $list = [];
            $rows = $this->PharmacyItems->find()
                ->select([$field])
                ->distinct([$field])
                ->where([
                    'PharmacyItems.is_delete' => 0,
                    "{$field} IS NOT" => null,
                    "{$field} !=" => '',
                ])
                ->orderBy([$field => 'ASC'])
                ->enableHydration(false)
                ->all();
            foreach ($rows as $r) {
                $list[$r[$field]] = $r[$field];
            }

            return $list;
        };
        $exportRegions = $distinct('region');
        $exportTypes = $distinct('type');
        $exportTherapies = $distinct('therapy_class');

        $this->set(compact('pharmacyItems', 'exportRegions', 'exportTypes', 'exportTherapies'));
    }

        /**
     * Export Excel
     */
    public function excel()
    {
        $this->request->allowMethod(['get']);

        $q         = trim((string)$this->request->getQuery('q'));
        $activeRaw = trim((string)$this->request->getQuery('is_active'));
        $regions   = array_values(array_filter((array)$this->request->getQuery('region')));
        $types     = array_values(array_filter((array)$this->request->getQuery('type')));
        $therapies = array_values(array_filter((array)$this->request->getQuery('therapy')));

        $query = $this->PharmacyItems->find()
            ->where(['PharmacyItems.is_delete' => 0]);

        if (!empty($regions)) {
            $query->where(['PharmacyItems.region IN' => $regions]);
        }
        if (!empty($types)) {
            $query->where(['PharmacyItems.type IN' => $types]);
        }
        if (!empty($therapies)) {
            $query->where(['PharmacyItems.therapy_class IN' => $therapies]);
        }
        if ($activeRaw === 'Y' || $activeRaw === 'N') {
            $query->where(['PharmacyItems.is_active' => $activeRaw]);
        }
        if ($q !== '') {
            $query->where(['OR' => [
                'PharmacyItems.code LIKE' => '%' . $q . '%',
                'PharmacyItems.name LIKE' => '%' . $q . '%',
                'PharmacyItems.region LIKE' => '%' . $q . '%',
                'PharmacyItems.type LIKE' => '%' . $q . '%',
                'PharmacyItems.therapy_class LIKE' => '%' . $q . '%',
                'PharmacyItems.producer LIKE' => '%' . $q . '%',
            ]]);
        }

        $query->orderBy(['PharmacyItems.name' => 'ASC']);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Pharmacy Item');

        $headers = [
            'No', 'Kode', 'Nama', 'Region', 'Type', 'Purchase Unit', 'Sale Unit',
            'QJB', 'Therapy Class', 'Producer', 'HPP', 'Sale Price', 'Status',
        ];
        $lastCol = 'M';

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
            $sheet->setCellValue("D{$row}", (string)($item->region ?: '-'));
            $sheet->setCellValue("E{$row}", (string)($item->type ?: '-'));
            $sheet->setCellValue("F{$row}", (string)($item->purchase_unit ?: '-'));
            $sheet->setCellValue("G{$row}", (string)($item->sale_unit ?: '-'));
            $sheet->setCellValue("H{$row}", $item->qjb !== null && $item->qjb !== '' ? (float)$item->qjb : '');
            $sheet->setCellValue("I{$row}", (string)($item->therapy_class ?: '-'));
            $sheet->setCellValue("J{$row}", (string)($item->producer ?: '-'));
            $sheet->setCellValue("K{$row}", $item->hpp !== null && $item->hpp !== '' ? (float)$item->hpp : '');
            $sheet->setCellValue("L{$row}", $item->sale_price !== null && $item->sale_price !== '' ? (float)$item->sale_price : '');
            $sheet->setCellValue("M{$row}", $item->is_active === 'Y' ? 'Aktif' : 'Nonaktif');
            $row++;
        }
        $last = max(2, $row - 1);
        $sheet->getStyle("H2:H{$last}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("K2:L{$last}")->getNumberFormat()->setFormatCode('#,##0.00');

        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->freezePane('A2');

        $tmp = tempnam(sys_get_temp_dir(), 'ph_');
        (new Xlsx($spreadsheet))->save($tmp);
        $body = file_get_contents($tmp);
        unlink($tmp);

        return $this->response
            ->withType('xlsx')
            ->withDownload('pharmacy_item_' . date('Ymd_His') . '.xlsx')
            ->withStringBody($body);
    }

    /**
     * View method
     *
     * @param string|null $id Pharmacy Item id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $pharmacyItem = $this->PharmacyItems->get($id, contain: []);

        $this->set(compact('pharmacyItem'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $pharmacyItem = $this->PharmacyItems->newEmptyEntity();
        if ($this->request->is('post')) {
            $pharmacyItem = $this->PharmacyItems->patchEntity($pharmacyItem, $this->request->getData());
                        $code = trim((string)$this->request->getData('code'));
            $dup = $this->PharmacyItems->exists(['code' => $code, 'is_delete' => 0]);

            if ($dup) {
                $this->Flash->error('Kode barang ' . $code . ' sudah dipakai.');
            } elseif ($this->PharmacyItems->save($pharmacyItem)) {
                $this->Flash->success(__('The pharmacy item has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The pharmacy item could not be saved. Please, try again.'));
            } {
                $this->Flash->success(__('The pharmacy item has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The pharmacy item could not be saved. Please, try again.'));
        }
        $this->set(compact('pharmacyItem'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Pharmacy Item id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $pharmacyItem = $this->PharmacyItems->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $pharmacyItem = $this->PharmacyItems->patchEntity($pharmacyItem, $this->request->getData());
            if ($this->PharmacyItems->save($pharmacyItem)) {
                $this->Flash->success(__('The pharmacy item has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The pharmacy item could not be saved. Please, try again.'));
        }
        $this->set(compact('pharmacyItem'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Pharmacy Item id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $pharmacyItem = $this->PharmacyItems->get($id);

        $pharmacyItem->is_delete = 1;
        $pharmacyItem->is_active = 'N';
        $pharmacyItem->deleted = \Cake\I18n\DateTime::now();

        if ($this->PharmacyItems->save($pharmacyItem)) {
            $this->Flash->success(__('The pharmacy item has been deleted.'));
        } else {
            $this->Flash->error(__('The pharmacy item could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
