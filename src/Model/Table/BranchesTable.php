<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Branches Model
 *
 * @property \App\Model\Table\OrdersTable&\Cake\ORM\Association\HasMany $Orders
 * @property \App\Model\Table\RecipientPayersTable&\Cake\ORM\Association\HasMany $RecipientPayers
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\HasMany $Users
 *
 * @method \App\Model\Entity\Branch newEmptyEntity()
 * @method \App\Model\Entity\Branch newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Branch[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Branch get($primaryKey, $options = [])
 * @method \App\Model\Entity\Branch findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Branch patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Branch[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Branch|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Branch saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Branch[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Branch[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Branch[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Branch[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class BranchesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('branches');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Orders', [
            'foreignKey' => 'branch_id',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('code')
            ->maxLength('code', 50)
            ->allowEmptyString('code');

        $validator
            ->scalar('name')
            ->maxLength('name', 50)
            ->allowEmptyString('name');

        $validator
            ->scalar('company_name')
            ->maxLength('company_name', 255)
            ->allowEmptyString('company_name');

        $validator
            ->scalar('company_address')
            ->maxLength('company_address', 255)
            ->allowEmptyString('company_address');

        $validator
            ->scalar('business_fields')
            ->maxLength('business_fields', 255)
            ->allowEmptyString('business_fields');

        $validator
            ->scalar('company_telp')
            ->maxLength('company_telp', 255)
            ->allowEmptyString('company_telp');

        $validator
            ->scalar('company_fax')
            ->maxLength('company_fax', 255)
            ->allowEmptyString('company_fax');

        $validator
            ->scalar('company_npwp')
            ->maxLength('company_npwp', 255)
            ->allowEmptyString('company_npwp');

        $validator
            ->scalar('company_director')
            ->maxLength('company_director', 255)
            ->allowEmptyString('company_director');

        $validator
            ->scalar('company_director_1')
            ->maxLength('company_director_1', 255)
            ->allowEmptyString('company_director_1');

        $validator
            ->scalar('sign_title_1')
            ->maxLength('sign_title_1', 255)
            ->allowEmptyString('sign_title_1');

        $validator
            ->scalar('company_director_2')
            ->maxLength('company_director_2', 255)
            ->allowEmptyString('company_director_2');

        $validator
            ->scalar('sign_title_2')
            ->maxLength('sign_title_2', 255)
            ->allowEmptyString('sign_title_2');

        $validator
            ->scalar('company_director_3')
            ->maxLength('company_director_3', 255)
            ->allowEmptyString('company_director_3');

        $validator
            ->scalar('sign_title_3')
            ->maxLength('sign_title_3', 255)
            ->allowEmptyString('sign_title_3');

        $validator
            ->scalar('company_director_4')
            ->maxLength('company_director_4', 255)
            ->allowEmptyString('company_director_4');

        $validator
            ->scalar('sign_title_4')
            ->maxLength('sign_title_4', 255)
            ->allowEmptyString('sign_title_4');

        $validator
            ->scalar('warehouse_address')
            ->maxLength('warehouse_address', 255)
            ->allowEmptyString('warehouse_address');

        $validator
            ->scalar('warehouse_telp')
            ->maxLength('warehouse_telp', 255)
            ->allowEmptyString('warehouse_telp');

        $validator
            ->scalar('warehouse_fax')
            ->maxLength('warehouse_fax', 255)
            ->allowEmptyString('warehouse_fax');

        $validator
            ->scalar('manifest_number')
            ->maxLength('manifest_number', 255)
            ->allowEmptyString('manifest_number');

        $validator
            ->scalar('invoice_code')
            ->maxLength('invoice_code', 255)
            ->allowEmptyString('invoice_code');

        $validator
            ->scalar('invoice_number')
            ->maxLength('invoice_number', 255)
            ->allowEmptyString('invoice_number');

        $validator
            ->scalar('invoice_number_include')
            ->maxLength('invoice_number_include', 255)
            ->allowEmptyString('invoice_number_include');

        $validator
            ->scalar('invoice_evidence_email')
            ->maxLength('invoice_evidence_email', 255)
            ->allowEmptyString('invoice_evidence_email');

        $validator
            ->scalar('faktur_pajak_number')
            ->maxLength('faktur_pajak_number', 255)
            ->allowEmptyString('faktur_pajak_number');

        $validator
            ->scalar('berita_acara_number')
            ->maxLength('berita_acara_number', 255)
            ->allowEmptyString('berita_acara_number');

        $validator
            ->scalar('permit_number')
            ->maxLength('permit_number', 255)
            ->allowEmptyString('permit_number');

        $validator
            ->scalar('offer_number')
            ->maxLength('offer_number', 255)
            ->allowEmptyString('offer_number');

        $validator
            ->scalar('offer_year')
            ->maxLength('offer_year', 255)
            ->allowEmptyString('offer_year');

        $validator
            ->scalar('bank')
            ->maxLength('bank', 255)
            ->allowEmptyString('bank');

        $validator
            ->scalar('norek')
            ->maxLength('norek', 255)
            ->allowEmptyString('norek');

        $validator
            ->scalar('an')
            ->maxLength('an', 255)
            ->allowEmptyString('an');

        $validator
            ->scalar('bank_name_1')
            ->maxLength('bank_name_1', 255)
            ->allowEmptyString('bank_name_1');

        $validator
            ->scalar('bank_norek_1')
            ->maxLength('bank_norek_1', 255)
            ->allowEmptyString('bank_norek_1');

        $validator
            ->scalar('bank_an_1')
            ->maxLength('bank_an_1', 255)
            ->allowEmptyString('bank_an_1');

        $validator
            ->scalar('bank_name_2')
            ->maxLength('bank_name_2', 255)
            ->allowEmptyString('bank_name_2');

        $validator
            ->scalar('bank_norek_2')
            ->maxLength('bank_norek_2', 255)
            ->allowEmptyString('bank_norek_2');

        $validator
            ->scalar('bank_an_2')
            ->maxLength('bank_an_2', 255)
            ->allowEmptyString('bank_an_2');

        $validator
            ->scalar('bank_name_3')
            ->maxLength('bank_name_3', 255)
            ->allowEmptyString('bank_name_3');

        $validator
            ->scalar('bank_norek_3')
            ->maxLength('bank_norek_3', 255)
            ->allowEmptyString('bank_norek_3');

        $validator
            ->scalar('bank_an_3')
            ->maxLength('bank_an_3', 255)
            ->allowEmptyString('bank_an_3');

        $validator
            ->scalar('bank_name_4')
            ->maxLength('bank_name_4', 255)
            ->allowEmptyString('bank_name_4');

        $validator
            ->scalar('bank_norek_4')
            ->maxLength('bank_norek_4', 255)
            ->allowEmptyString('bank_norek_4');

        $validator
            ->scalar('bank_an_4')
            ->maxLength('bank_an_4', 255)
            ->allowEmptyString('bank_an_4');

        $validator
            ->scalar('bank_name_5')
            ->maxLength('bank_name_5', 255)
            ->allowEmptyString('bank_name_5');

        $validator
            ->scalar('bank_norek_5')
            ->maxLength('bank_norek_5', 255)
            ->allowEmptyString('bank_norek_5');

        $validator
            ->scalar('bank_an_5')
            ->maxLength('bank_an_5', 255)
            ->allowEmptyString('bank_an_5');

        $validator
            ->scalar('va_bank_an')
            ->maxLength('va_bank_an', 255)
            ->allowEmptyString('va_bank_an');

        $validator
            ->scalar('va_bank_name')
            ->maxLength('va_bank_name', 255)
            ->allowEmptyString('va_bank_name');

        $validator
            ->scalar('va_bank_branch')
            ->maxLength('va_bank_branch', 255)
            ->allowEmptyString('va_bank_branch');

        $validator
            ->scalar('va_bank_norek')
            ->maxLength('va_bank_norek', 255)
            ->allowEmptyString('va_bank_norek');

        $validator
            ->scalar('wa_invoice')
            ->maxLength('wa_invoice', 255)
            ->allowEmptyString('wa_invoice');

        $validator
            ->scalar('city_invoice')
            ->maxLength('city_invoice', 255)
            ->allowEmptyString('city_invoice');

        $validator
            ->email('email')
            ->allowEmptyString('email');

        $validator
            ->scalar('emergency_call')
            ->maxLength('emergency_call', 255)
            ->allowEmptyString('emergency_call');

        $validator
            ->scalar('offer_perihal')
            ->maxLength('offer_perihal', 255)
            ->allowEmptyString('offer_perihal');

        $validator
            ->scalar('offer_description')
            ->allowEmptyString('offer_description');

        $validator
            ->scalar('offer_info')
            ->maxLength('offer_info', 255)
            ->allowEmptyString('offer_info');

        $validator
            ->scalar('manifest_type')
            ->maxLength('manifest_type', 50)
            ->allowEmptyString('manifest_type');

        $validator
            ->scalar('manifest_code')
            ->maxLength('manifest_code', 50)
            ->allowEmptyString('manifest_code');

        $validator
            ->scalar('offer_code')
            ->maxLength('offer_code', 100)
            ->allowEmptyString('offer_code');

        $validator
            ->scalar('sign_1')
            ->maxLength('sign_1', 100)
            ->allowEmptyString('sign_1');

        $validator
            ->scalar('sign_2')
            ->maxLength('sign_2', 100)
            ->allowEmptyString('sign_2');

        $validator
            ->scalar('sign_3')
            ->maxLength('sign_3', 100)
            ->allowEmptyString('sign_3');

        $validator
            ->scalar('sign_4')
            ->maxLength('sign_4', 100)
            ->allowEmptyString('sign_4');

        $validator
            ->scalar('mou_code')
            ->maxLength('mou_code', 100)
            ->allowEmptyString('mou_code');

        $validator
            ->scalar('header')
            ->maxLength('header', 100)
            ->allowEmptyString('header');

        $validator
            ->scalar('footer')
            ->maxLength('footer', 100)
            ->allowEmptyString('footer');

        $validator
            ->scalar('desc')
            ->maxLength('desc', 255)
            ->allowEmptyString('desc');

        $validator
            ->scalar('is_create')
            ->maxLength('is_create', 50)
            ->allowEmptyString('is_create');

        $validator
            ->scalar('is_update')
            ->maxLength('is_update', 50)
            ->allowEmptyString('is_update');

        $validator
            ->scalar('is_delete')
            ->maxLength('is_delete', 50)
            ->allowEmptyString('is_delete');

        $validator
            ->dateTime('deleted')
            ->allowEmptyDateTime('deleted');

        $validator
            ->scalar('status')
            ->maxLength('status', 50)
            ->allowEmptyString('status');

        $validator
            ->scalar('is_active')
            ->allowEmptyString('is_active');

        $validator
            ->scalar('is_log')
            ->allowEmptyString('is_log');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {

        return $rules;
    }
}
