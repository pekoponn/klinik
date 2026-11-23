<?php
declare(strict_types=1);

namespace App\Controller\Penunjang;

use App\Controller\AppController;
use Cake\I18n\DateTime;

/**
 * Isi Nilai Normal / Template Radiologi.
 * Daftar tindakan diambil dari Master Tindakan, hanya yang berstatus radiologi.
 * Nilai normal disimpan di tabel radiology_normal_values (lihat radiology_normal_values.sql).
 *
 * Lokasi file: src/Controller/Penunjang/RadiologyNormalValuesController.php
 */
class RadiologyNormalValuesController extends AppController
{
    // ====== SESUAIKAN DENGAN SKEMA MASTER TINDAKAN ANDA ======
    private const TINDAKAN_TABLE = 'MasterTindakan';   // nama Table class master tindakan
    private const FILTER_FIELD   = 'kategori';         // kolom yang menandai jenis tindakan
    private const FILTER_VALUE   = 'Radiologi';        // isi kolom untuk tindakan radiologi
    private const CODE_FIELD     = 'kode';             // kolom kode tindakan
    private const NAME_FIELD     = 'nama';             // kolom nama tindakan

    private const DETAIL_FIELDS = [
        'Kode'       => 'kode',
        'Kategori'   => 'kategori',
        'Tindakan'   => 'nama',
        'Keterangan' => 'keterangan',
    ];

    private const LIST_LIMIT = 300;

    public function index()
    {
        $q = trim((string)$this->request->getQuery('q', ''));
        $selectedId = (int)$this->request->getQuery('tindakan_id', 0);

        $Tindakan = $this->fetchTable(self::TINDAKAN_TABLE);
        $alias = $Tindakan->getAlias();

        $query = $Tindakan->find()
            ->where([$alias . '.' . self::FILTER_FIELD => self::FILTER_VALUE])
            ->orderBy([$alias . '.' . self::NAME_FIELD => 'ASC']);

        if ($q !== '') {
            $query->where(['OR' => [
                $alias . '.' . self::NAME_FIELD . ' LIKE' => '%' . $q . '%',
                $alias . '.' . self::CODE_FIELD . ' LIKE' => '%' . $q . '%',
            ]]);
        }

        $total = (clone $query)->count();
        $list = $query->limit(self::LIST_LIMIT)->all()->toList();

        // Tindakan terpilih: dari URL, jika tidak ada pakai yang pertama di daftar
        $selected = null;
        if ($selectedId > 0) {
            $selected = $Tindakan->find()
                ->where([
                    $alias . '.id' => $selectedId,
                    $alias . '.' . self::FILTER_FIELD => self::FILTER_VALUE,
                ])
                ->first();
        }
        if (!$selected && $list) {
            $selected = $list[0];
        }

        $normalValue = '';
        if ($selected) {
            $row = $this->fetchTable('RadiologyNormalValues')->find()
                ->where(['tindakan_id' => $selected->get('id')])
                ->first();
            $normalValue = (string)($row ? $row->get('normal_value') : '');
        }

        $this->set([
            'list' => $list,
            'total' => $total,
            'limit' => self::LIST_LIMIT,
            'selected' => $selected,
            'normalValue' => $normalValue,
            'q' => $q,
            'codeField' => self::CODE_FIELD,
            'nameField' => self::NAME_FIELD,
            'detailFields' => self::DETAIL_FIELDS,
        ]);
    }

    public function save()
    {
        $this->request->allowMethod(['post']);

        $id = (int)$this->request->getData('tindakan_id');
        $q = (string)$this->request->getData('q', '');
        $back = ['action' => 'index', '?' => ['tindakan_id' => $id, 'q' => $q]];

        $Tindakan = $this->fetchTable(self::TINDAKAN_TABLE);
        $alias = $Tindakan->getAlias();

        // Pastikan tindakan memang berstatus radiologi
        $valid = $Tindakan->exists([
            $alias . '.id' => $id,
            $alias . '.' . self::FILTER_FIELD => self::FILTER_VALUE,
        ]);
        if (!$valid) {
            $this->Flash->error('Tindakan radiologi tidak ditemukan.');
            return $this->redirect(['action' => 'index']);
        }

        $Normal = $this->fetchTable('RadiologyNormalValues');
        $row = $Normal->find()->where(['tindakan_id' => $id])->first();
        $now = DateTime::now();

        if (!$row) {
            $row = $Normal->newEmptyEntity();
            $row->set('tindakan_id', $id);
            $row->set('created', $now);
        }
        $row->set('normal_value', (string)$this->request->getData('normal_value', ''));
        $row->set('modified', $now);

        if ($Normal->save($row)) {
            $this->Flash->success('Nilai normal radiologi disimpan.');
        } else {
            $this->Flash->error('Gagal menyimpan nilai normal.');
        }

        return $this->redirect($back);
    }
}