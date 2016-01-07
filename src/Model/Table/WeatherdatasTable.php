<?php
namespace Openweathermap\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Openweathermap\Model\Entity\Weatherdata;

/**
 * Weatherdatas Model
 *
 */
class WeatherdatasTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('weatherdatas');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('location', 'create')
            ->notEmpty('location');

        $validator
            ->requirePresence('loc_type', 'create')
            ->notEmpty('loc_type');

        $validator
            ->requirePresence('loc_value', 'create')
            ->notEmpty('loc_value');

        $validator
            ->requirePresence('datas', 'create')
            ->notEmpty('datas');

        return $validator;
    }

}
