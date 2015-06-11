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
	
	 /**
     * getDataByParamsSubgroupType method
     * @param array $conditions Conditions on which to search. {DEFAULT : empty}
     * @param array $fields Fields to fetch. {DEFAULT : empty}
     * @return void
     */
	 
    public function getDataByParamsSubgroupType(array $fields, array $conditions)
    {
       return $this->SubgroupTypeObj->getDataByParams($fields,$conditions);
	
    }
	
	 /**
     * getDataByParamsSubgroup method
     * @param array $conditions Conditions on which to search. {DEFAULT : empty}
     * @param array $fields Fields to fetch. {DEFAULT : empty}
     * @return void
     */
	 
    public function getDataByParamsSubgroup(array $fields, array $conditions)
    {
       return $this->SubgroupObj->getDataByParams($fields,$conditions);
	
    }
	
	/**
    *  getDataBySubgroupTypeName method
    *  @param $Subgroup_Type_Name The value on which you will get all details corresponding to the  Subgroup type name.
    *  @return  array
    */
	 
    public function getDataBySubgroupTypeName($Subgroup_Type_Name)
    {        
		  return $this->SubgroupTypeObj->getDataBySubgroupTypeName($Subgroup_Type_Name);
	
    }
	
	/**
    *  getDataBySubgroupName method
    *  @param $Subgroup_Type_Name The value on which you will get all details corresponding to the  Subgroup type name.
    *  @return  array
    */
	 
    public function getDataBySubgroupName($Subgroup_Type_Name)
    {        
		  return $this->SubgroupObj->getDataBySubgroupName($Subgroup_Type_Name);
	
    }
	
	
	
	 


}
