<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Subgroup Entity.
 */
class Subgroup extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
   protected $_accessible = [
        'Subgroup_NId' => true,
        'Subgroup_Name' => true,
        'Subgroup_GId' => true,
        'Subgroup_Global' => true,
        'Subgroup_Type' => true,      
        'Subgroup_Order' => true       
    ];
}
