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

    public function beforeFilter()
    {
        $this->SubgroupTypeObj = TableRegistry::get('SubgroupType');
    }
	

   /**
     * getDataByIds method
     *
     * @param array $conditions Conditions on which to search. {DEFAULT : empty}
     * @param array $fields Fields to fetch. {DEFAULT : empty}
     * @return void
     */
	 
	 
    public function getDataByIds($ids = null, $fields = [], $type = 'all' )
    {
        
        $UTIndicatorEn = TableRegistry::get('Indicator');


        return $UTIndicatorEn->getDataByIds($ids, $fields, $type);

    }


    /**
     * getDataByParams method
     *
     * @param array $conditions Conditions on which to search. {DEFAULT : empty}
     * @param array $fields Fields to fetch. {DEFAULT : empty}
     * @return void
     */
    public function getDataByParams(array $conditions, array $fields)
    {
        
        App::import('Model', 'Indicator');
        $UTIndicatorEn = new UTIndicatorEn();

        return $UTIndicatorEn->getDataByParams($conditions, $fields);

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


}
