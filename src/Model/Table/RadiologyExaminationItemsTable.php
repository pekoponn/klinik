<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;

class RadiologyExaminationItemsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('radiology_examination_items');
        $this->setDisplayField('examination_name');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');

        $this->belongsTo('RadiologyExaminations', [
            'foreignKey' => 'examination_id',
            'joinType' => 'INNER',
        ]);
    }
}