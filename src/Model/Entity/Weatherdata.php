<?php
namespace Openweathermap\Model\Entity;

use Cake\ORM\Entity;

/**
 * Weatherdata Entity.
 *
 * @property int $id
 * @property string $location
 * @property string $loc_type
 * @property string $loc_value
 * @property string $datas
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 */
class Weatherdata extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
