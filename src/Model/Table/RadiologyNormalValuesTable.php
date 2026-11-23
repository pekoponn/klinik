<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * Simpan di: src/Model/Table/RadiologyNormalValuesTable.php
 */
class RadiologyNormalValuesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('radiology_normal_values');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }
}