<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;

/**
 * Subgroup Component
 */
class SubgroupComponent extends Component
{

   

    public $SubgroupTypeObj = NULL;
    public $SubgroupObj     = NULL;
  

    public function beforeFilter()
    {
        $this->SubgroupTypeObj = TableRegistry::get('SubgroupType');
        $this->SubgroupObj     = TableRegistry::get('Subgroup');
    }
	

   
	 /**
     * insertDataSubgroupType method is used to add new subgroup type      *
   	 * @param fieldsArray is passed as posted data  
     * @return void
     */
	 
    public function insertDataSubgroupType($fieldsArray)
    {
        return $this->SubgroupTypeObj->insertData($fieldsArray);

    }
	
	 /**
     * insertDataSubgroup method is used to add new subgroup  *
   	 * @param fieldsArray is passed as posted data  
     * @return void
     */
	 
    public function insertDataSubgroup($fieldsArray)
    {
       return $this->SubgroupObj->insertData($fieldsArray);
	
    }


}
