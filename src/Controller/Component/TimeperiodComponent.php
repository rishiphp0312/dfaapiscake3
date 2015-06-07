<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;

/**
 * Time period Component
 */
class TimeperiodComponent extends Component
{

    /**
     * getTimeperiodDataById method     *
     * @param array $field columns which will be fetched from table. {DEFAULT : empty}
	 * @param type  by default its value will be all user can pass  first or count also
     * @return void
    */
    public function getTimeperiodDataById($id = null, $field = [], $type = 'all' )
    {
        
        $UTTimeperiod = TableRegistry::get('UTTimeperiod');

        return $UTTimeperiod->getTimePeriodById($id, $field, $type);

    }


    /**
     * getTimePeriodDataByParams method     *  
     * @param array $parameters Conditions on which data will be filtered. {DEFAULT : empty}
     * @param array $field columns which will be fetched from table. {DEFAULT : empty}
	 * @param type  by default its value will be all user can pass  first or count also
     * @return void
    */
	 
    public function getTimePeriodDataByParams(array $parameters, array $fields,$type='all')
    {
        //        App::import('Model', 'UTIndicatorEn');
        //        $UTIndicatorEn = new UTIndicatorEn();        
		$UTTimeperiod = TableRegistry::get('UTTimeperiod');
        return $UTIndicatorEn->getTimePeriodByParams($parameters, $fields);
    }
	
	/**
     * savesingleTimePeriodData method     *  
     * @param array $timeperiodvalue timeperiod which will be saved in database if exists already nothing will be happened. {DEFAULT : empty}
     * @return void
    */
	 
    public function savesingleTimePeriodData($timeperiodvalue=null)
    {
		//        App::import('Model', 'UTIndicatorEn');
        //        $UTIndicatorEn = new UTIndicatorEn();
        
		$UTTimeperiod = TableRegistry::get('UTTimeperiod');
        return $UTTimeperiod->savesingleTimePeriod($timeperiodvalue);

    }
	
	
	/**
     * @param $timeperiodvalue the timeperiod value which needs to be deleted .
     * @return void
    */
	 
    public function deletesingleTimePeriod($timeperiodvalue)
    {
        
		$UTTimeperiod = TableRegistry::get('UTTimeperiod');
        return $UTTimeperiod->deletesingleTimePeriod($timeperiodvalue);

    }
	
	
	/**
     * @param $timeperiodvalue the timeperiod value according to which records will be filtered .
     * @return void
    */
	 
    public function getDataByTimeperiod($timeperiodvalue)
    {
       
		$UTTimeperiod = TableRegistry::get('UTTimeperiod');
        return $UTTimeperiod->getDataByTimeperiod($timeperiodvalue);

    }
	
	


}
