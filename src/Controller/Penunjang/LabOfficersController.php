<?php
declare(strict_types=1);

namespace App\Controller\Penunjang;

use App\Controller\AppController;

class LabOfficersController extends AppController
{
    private const STAFF_TABLE   = 'MasterTenagaMedis';    
    private const STATUS_FIELD  = 'status';                 
    private const STATUS_VALUE  = 'Analis Laboratorium';   
    private const CODE_FIELD    = 'kode';                  
    private const NAME_FIELD    = 'nama';                   
    private const ACTIVE_FIELD  = 'is_active';              

    public function index()
    {
        $q = trim((string)$this->request->getQuery('q', ''));

        $staffs = $this->fetchTable(self::STAFF_TABLE);
        $alias  = $staffs->getAlias();

        $query = $staffs->find()
            ->where([$alias . '.' . self::STATUS_FIELD => self::STATUS_VALUE])
            ->orderBy([$alias . '.' . self::NAME_FIELD => 'ASC']);

        if ($q !== '') {
            $query->where(['OR' => [
                $alias . '.' . self::NAME_FIELD . ' LIKE' => '%' . $q . '%',
                $alias . '.' . self::CODE_FIELD . ' LIKE' => '%' . $q . '%',
            ]]);
        }

        $officers = $this->paginate($query, ['limit' => 20]);

        $this->set([
            'officers'    => $officers,
            'q'           => $q,
            'statusLabel' => self::STATUS_VALUE,
            'codeField'   => self::CODE_FIELD,
            'nameField'   => self::NAME_FIELD,
            'statusField' => self::STATUS_FIELD,
            'activeField' => self::ACTIVE_FIELD,
        ]);
    }
}