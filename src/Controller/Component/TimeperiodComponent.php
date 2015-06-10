<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;

/**
 * Time period Component
 */
class TimeperiodComponent extends Component
{

    
	public $TimeperiodObj = NULL;

    public function beforeFilter()
    {
        $this->TimeperiodObj = TableRegistry::get('TimePeriod');
    }
	
	
	
	/**
     * getDataByIds method
     *
     * @param array $ids the ids can be multiple or single to get filtered records . {DEFAULT : empty}
     * @param array $fields Fields to fetch. {DEFAULT : empty}
     * @param  $type the the type of list user needs it can be list or first or count . {DEFAULT : all}
     * @return void
    */
    public function getDataByIds($ids, $fields=[] , $type='all'){ 
        return $this->TimeperiodObj->getDataByIds($ids, $fields, $type);
    }


    /**
     * getDataByParams method
     * @param array $conditions Conditions on which to search. {DEFAULT : empty}
     * @param array $fields Fields to fetch. {DEFAULT : empty}
     * @return void
     */
    public function getDataByParams(array $fields,array $conditions){
        return $this->TimeperiodObj->getDataByParams($fields,$conditions);
    }
	
	/**
     * @param $timeperiodvalue the timeperiod value according to which records will be filtered .
     * @param $periodicity the periodicity value will be optional .
     * @return void
    */
	 
    public function getDataByTimeperiod($timeperiodvalue,$periodicity =''){      
        return $this->TimeperiodObj->getDataByTimeperiod($timeperiodvalue,$periodicity );

    }
	


    /**
     * deleteByIds method
     *
     * @param  $ids the ids which needs to be deleted . {DEFAULT : null}
     * @return void
     */
    public function deleteByIds($ids = null){
        return $this->TimeperiodObj->deleteByIds($ids);
    }


    /**
     * deleteByParams method
     *
     * @param array $conditions Fields to fetch. {DEFAULT : empty}
     * @return void
     */
    public function deleteByParams($conditions = []){
        return $this->TimeperiodObj->deleteByParams($conditions);
    }

	
	/**
     * @param $timeperiodvalue the timeperiod value which will be deleted .
     * @return void
    */
	 
    public function deleteByTimePeriod($timeperiodvalue){   
        return $this->TimeperiodObj->deleteByTimePeriod($timeperiodvalue);

    }
	

    /**
     * insertData method
     *
     * @param array $fieldsArray Fields to insert with their Data. {DEFAULT : empty}
     * @return void
     */
    public function insertData($fieldsArray = []){
        return $this->TimeperiodObj->insertData($fieldsArray);
    }

    
    /**
     * insertBulkData method
     *
     * @param array $fieldsArray Fields to insert with their Data. {DEFAULT : empty}
     * @return void
     */
    public function insertBulkData($fieldsArray = []){
        return $this->TimeperiodObj->insertBulkData($fieldsArray);
    }

	
	


    /**
     * getTimePeriodDataByParams method     *  
     * @param array $parameters Conditions on which data will be filtered. {DEFAULT : empty}
     * @param array $field columns which will be fetched from table. {DEFAULT : empty}
	 * @param type  by default its value will be all user can pass  first or count also
     * @return void
    */
	 
    public function getTimePeriodDataByParams(array $parameters, array $fields,$type='all'){
       
        return $this->TimeperiodObj->getTimePeriodByParams($parameters, $fields);
    }
	
	/**
     * savesingleTimePeriodData method     *  
     * @param array $timeperiodvalue timeperiod which will be saved in database if exists already nothing will be happened. {DEFAULT : empty}
     * @return void
    */
	 
    public function savesingleTimePeriodData($timeperiodvalue=null){
		
        return $this->TimeperiodObj->savesingleTimePeriod($timeperiodvalue);

    }
	
	
	
	
	
	


}
