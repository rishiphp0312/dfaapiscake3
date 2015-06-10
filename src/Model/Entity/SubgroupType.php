<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SubgroupType Entity.
 */
class SubgroupType extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
   protected $_accessible = [
        'Subgroup_Type_NId' => true,
        'Subgroup_Type_Name' => true,
        'Subgroup_Type_GID' => true,
        'Subgroup_Type_Order' => true,
        'Subgroup_Type_Global' => true        
    ];
}
