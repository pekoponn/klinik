<?php
declare(strict_types=1);

namespace App\Controller\Penunjang;

use App\Controller\AppController;

/**
 * Simpan di: src/Controller/Penunjang/LabNormalValuesController.php
 *
 * Sumber daftar tindakan sekarang: master_tindakan (kategori Laboratorium),
 * bukan master_laboratories lagi.
 *
 * Nama kolom relasi di lab_normal_values & lab_normal_templates
 * (master_laboratory_id) sengaja TIDAK diganti supaya view tidak perlu diubah.
 * Isinya sekarang berisi ID dari master_tindakan.
 */
class LabNormalValuesController extends AppController
{
    // Sesuaikan dengan isi kolom kategori di master_tindakan
    // (lihat $this->Krs->tindakanKategori()).
    private const KATEGORI_LAB = 'Laboratorium';

    public function index()
    {
        $q = trim((string)$this->request->getQuery('q'));
        $selectedId = (int)$this->request->getQuery('lab_id');

        $Tindakan = $this->fetchTable('MasterTindakan');

        $labsQuery = $Tindakan->find()
            ->select([
                'id' => 'MasterTindakan.id',
                'code' => 'MasterTindakan.kode',
                'name' => 'MasterTindakan.nama',
            ])
            ->where([
                'MasterTindakan.kategori' => self::KATEGORI_LAB,
                'MasterTindakan.is_active' => 1,
            ])
            ->orderBy(['MasterTindakan.nama' => 'ASC'])
            ->limit(300)
            ->disableHydration();
        $query->where([$alias . '.is_active' => 1]);
        if ($q !== '') {
            $labsQuery->where(['OR' => [
                'MasterTindakan.nama LIKE' => "%$q%",
                'MasterTindakan.kode LIKE' => "%$q%",
            ]]);
        }
        $labs = $labsQuery->all()->toList();

        if (!$selectedId && $labs) {
            $selectedId = (int)$labs[0]['id'];
        }

        $selectedLab = $selectedId
            ? $Tindakan->find()
                ->select([
                    'id' => 'MasterTindakan.id',
                    'code' => 'MasterTindakan.kode',
                    'name' => 'MasterTindakan.nama',
                    'lab_group' => 'MasterTindakan.lab_group',
                    'lab_subgroup' => 'MasterTindakan.lab_subgroup',
                ])
                ->where([
                    'MasterTindakan.id' => $selectedId,
                    'MasterTindakan.kategori' => self::KATEGORI_LAB,
                ])
                ->disableHydration()
                ->first()
            : null;

        $values = $selectedId
            ? $this->LabNormalValues->find()
                ->where(['master_laboratory_id' => $selectedId, 'is_active' => 'Y'])
                ->orderBy(['sort_order' => 'ASC', 'id' => 'ASC'])->all()
            : [];

        $templates = $selectedId
            ? $this->fetchTable('LabNormalTemplates')->find()
                ->where(['master_laboratory_id' => $selectedId, 'is_active' => 'Y'])
                ->orderBy(['id' => 'ASC'])->all()
            : [];

        $this->set(compact('labs', 'q', 'selectedId', 'selectedLab', 'values', 'templates'));
    }

    public function addValue()
    {
        $this->request->allowMethod(['post']);
        $labId = (int)$this->request->getData('master_laboratory_id');

        // simpan isian yang sedang diedit dulu
        foreach ((array)$this->request->getData('rows') as $id => $data) {
            $e = $this->LabNormalValues->find()->where(['id' => (int)$id, 'master_laboratory_id' => $labId])->first();
            if ($e) {
                foreach ($data as $k => $v) {
                    $data[$k] = ($v === '') ? null : $v;
                }
                $this->LabNormalValues->save($this->LabNormalValues->patchEntity($e, $data));
            }
        }

        $e = $this->LabNormalValues->newEntity([
            'master_laboratory_id' => $labId,
            'parameter_name' => 'Parameter baru',
            'decimals' => 0,
            'sort_order' => (int)$this->LabNormalValues->find()->where(['master_laboratory_id' => $labId])->count() + 1,
        ]);
        $this->LabNormalValues->save($e);
        return $this->redirect(['action' => 'index', '?' => ['lab_id' => $labId]]);
    }

    public function deleteTemplates()
    {
        $this->request->allowMethod(['post']);
        $labId = (int)$this->request->getData('master_laboratory_id');
        $ids = array_map('intval', (array)$this->request->getData('tpl_ids'));
        if ($ids) {
            $this->fetchTable('LabNormalTemplates')->updateAll(['is_active' => 'N'], ['id IN' => $ids]);
        }
        return $this->redirect(['action' => 'index', '?' => ['lab_id' => $labId]]);
    }

    public function save()
    {
        $this->request->allowMethod(['post']);
        $labId = (int)$this->request->getData('master_laboratory_id');
        $rows = (array)$this->request->getData('rows');

        // Golongan & Sub disimpan di master_tindakan
        $this->fetchTable('MasterTindakan')->updateAll([
            'lab_group' => trim((string)$this->request->getData('lab_group')) ?: null,
            'lab_subgroup' => trim((string)$this->request->getData('lab_subgroup')) ?: null,
        ], ['id' => $labId]);

        foreach ($rows as $id => $data) {
            $e = $this->LabNormalValues->find()->where(['id' => (int)$id, 'master_laboratory_id' => $labId])->first();
            if (!$e) {
                continue;
            }
            foreach ($data as $k => $v) {
                $data[$k] = ($v === '') ? null : $v;
            }
            $data['is_update'] = 1;
            $this->LabNormalValues->save($this->LabNormalValues->patchEntity($e, $data));
        }

        $this->Flash->success('Nilai normal tersimpan.');
        return $this->redirect(['action' => 'index', '?' => ['lab_id' => $labId]]);
    }

    public function deleteValue($id = null)
    {
        $this->request->allowMethod(['post']);
        $e = $this->LabNormalValues->get((int)$id);
        $labId = $e->master_laboratory_id;
        $e->is_active = 'N';
        $e->is_delete = 1;
        $this->LabNormalValues->save($e);
        return $this->redirect(['action' => 'index', '?' => ['lab_id' => $labId]]);
    }

    public function addTemplate()
    {
        $this->request->allowMethod(['post']);
        $labId = (int)$this->request->getData('master_laboratory_id');
        $name = trim((string)$this->request->getData('test_name'));

        if ($name !== '') {
            $t = $this->fetchTable('LabNormalTemplates');
            $t->save($t->newEntity([
                'master_laboratory_id' => $labId,
                'test_name' => $name,
                'normal_result' => trim((string)$this->request->getData('normal_result')),
            ]));
        }
        return $this->redirect(['action' => 'index', '?' => ['lab_id' => $labId]]);
    }

    public function deleteTemplate($id = null)
    {
        $this->request->allowMethod(['post']);
        $t = $this->fetchTable('LabNormalTemplates');
        $e = $t->get((int)$id);
        $labId = $e->master_laboratory_id;
        $e->is_active = 'N';
        $t->save($e);
        return $this->redirect(['action' => 'index', '?' => ['lab_id' => $labId]]);
    }

    // Hapus parameter yang dicentang
    public function deleteChecked()
    {
        $this->request->allowMethod(['post']);
        $labId = (int)$this->request->getData('master_laboratory_id');
        $ids = array_map('intval', (array)$this->request->getData('check_ids'));

        if ($ids) {
            $this->LabNormalValues->updateAll(
                ['is_active' => 'N', 'is_delete' => 1],
                ['id IN' => $ids, 'master_laboratory_id' => $labId]
            );
            $this->Flash->success(count($ids) . ' parameter dihapus.');
        } else {
            $this->Flash->warning('Centang parameter yang mau dihapus.');
        }
        return $this->redirect(['action' => 'index', '?' => ['lab_id' => $labId]]);
    }

    // Salin semua parameter dari pemeriksaan lain ke pemeriksaan terpilih
    public function copyFrom()
    {
        $this->request->allowMethod(['post']);
        $labId = (int)$this->request->getData('master_laboratory_id');
        $srcId = (int)$this->request->getData('source_laboratory_id');

        if ($srcId && $srcId !== $labId) {
            $rows = $this->LabNormalValues->find()
                ->where(['master_laboratory_id' => $srcId, 'is_active' => 'Y'])
                ->disableHydration()->all();
            $n = 0;
            foreach ($rows as $r) {
                unset($r['id'], $r['created'], $r['modified']);
                $r['master_laboratory_id'] = $labId;
                $this->LabNormalValues->save($this->LabNormalValues->newEntity($r));
                $n++;
            }
            $this->Flash->success("$n parameter disalin.");
        }
        return $this->redirect(['action' => 'index', '?' => ['lab_id' => $labId]]);
    }
}