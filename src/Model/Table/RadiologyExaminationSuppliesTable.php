<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;

class RadiologyExaminationSuppliesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('radiology_examination_supplies');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');

        $this->belongsTo('RadiologyExaminations', [
            'foreignKey' => 'examination_id',
            'joinType' => 'INNER',
        ]);
    }
}