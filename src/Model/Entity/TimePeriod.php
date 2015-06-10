<?php  
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * TimePeriod Entity.
 */
class TimePeriod extends Entity
{

  /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
	  protected $_accessible = [
        'TimePeriod_NId' => true,
        'TimePeriod' => true,
        'StartDate' => true,
        'EndDate' => true,
        'Periodicity' => true
    ];
   

}