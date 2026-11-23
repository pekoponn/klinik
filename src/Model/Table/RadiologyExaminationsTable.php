<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;

class RadiologyExaminationsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('radiology_examinations');
        $this->setDisplayField('rad_no');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');

        $this->hasMany('RadiologyExaminationItems', [
            'foreignKey' => 'examination_id',
            'dependent' => true,
        ]);

        $this->hasMany('RadiologyExaminationSupplies', [
            'foreignKey' => 'examination_id',
            'dependent' => true,
        ]);
    }
}