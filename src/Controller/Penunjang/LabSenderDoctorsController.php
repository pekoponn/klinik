<?php
declare(strict_types=1);

namespace App\Controller\Penunjang;

use App\Controller\AppController;

class LabSenderDoctorsController extends AppController
{
    protected ?string $defaultTable = 'MasterPatientReferrers';

    public function index()
    {
        $q = trim((string)$this->request->getQuery('q'));

        $query = $this->fetchTable('MasterPatientReferrers')->find()
            ->orderBy(['MasterPatientReferrers.name' => 'ASC']);

        if ($q !== '') {
            $query->where(['OR' => [
                'MasterPatientReferrers.code LIKE' => '%' . $q . '%',
                'MasterPatientReferrers.name LIKE' => '%' . $q . '%',
                'MasterPatientReferrers.address LIKE' => '%' . $q . '%',
            ]]);
        }

        $doctors = $this->paginate($query, ['limit' => 50]);
        $this->set(compact('doctors', 'q'));
    }

    /**
     * Tambah (tanpa id) atau ubah (dengan id), dipanggil dari modal.
     */
    public function save()
    {
        $this->request->allowMethod(['post']);

        $Table = $this->fetchTable('MasterPatientReferrers');

        $data = $this->request->getData();
        $id = (int)($data['id'] ?? 0);
        unset($data['id']);

        if ($id > 0) {
            $doctor = $Table->get($id);
        } else {
            $doctor = $Table->newEmptyEntity();
            if (trim((string)($data['code'] ?? '')) === '') {
                $row = $Table->find()->select(['max_id' => 'MAX(id)'])->disableHydration()->first();
                $data['code'] = 'PRJ-' . str_pad((string)(((int)($row['max_id'] ?? 0)) + 1), 4, '0', STR_PAD_LEFT);
            }
            $data['referral_origin'] = 'Dokter Luar';
        }

        $data['name'] = strtoupper(trim((string)($data['name'] ?? '')));
        $data['doctor_sender'] = $data['name'];
        $data['is_active'] = !empty($data['is_active']) ? 'Y' : 'N';

        $doctor = $Table->patchEntity($doctor, $data);
        if ($Table->save($doctor)) {
            $this->Flash->success('Data dokter pengirim tersimpan.');
        } else {
            $messages = [];
            foreach ($doctor->getErrors() as $errs) {
                $messages = array_merge($messages, array_values((array)$errs));
            }
            $this->Flash->error('Gagal menyimpan: ' . implode(', ', $messages));
        }

        return $this->redirect($this->referer(['action' => 'index']));
    }

    /**
     * Mengikuti Master Perujuk: data dinonaktifkan, bukan dihapus.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        $Table = $this->fetchTable('MasterPatientReferrers');
        $doctor = $Table->get($id);
        $doctor->is_active = 'N';

        if ($Table->save($doctor)) {
            $this->Flash->success('Data dokter pengirim dinonaktifkan.');
        } else {
            $this->Flash->error('Data tidak dapat dinonaktifkan.');
        }

        return $this->redirect(['action' => 'index']);
    }
}