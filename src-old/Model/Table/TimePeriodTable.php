<?php  
namespace App\Model\Table;

use App\Model\Entity\User;
use Cake\ORM\Table;


/**
 * TimePeriodTable Model
 */
class TimePeriodTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('UT_TimePeriod');
        $this->primaryKey('TimePeriod_NId');
        $this->addBehavior('Timestamp');
    }

	
	 /**
     * getDataByIds method
     * @param array $id The WHERE conditions with ids only for the Query. {DEFAULT : null}
     * @param array $fields The Fields to SELECT from the Query. {DEFAULT : empty}
     * @return void
     */
    public function getDataByIds($ids = null, array $fields, $type = 'all' ){
        $options = [];
		
        if(isset($fields) && !empty($fields))
        $options['fields'] = $fields;	    		
	
        if(isset($ids) && !empty($ids))
        $options['conditions'] = ['TimePeriod_NId IN'=>$ids];

		if($type=='list'){
			
			  if(isset($fields) && !empty($fields))
              $options['fields'] = array('keyField' => $fields[0],'valueField' => $fields[1]);		      
			  
			  $query = $this->find($type, $options['fields'],$options['conditions']);	
		
		}else{
			
    		  $query = $this->find($type, $options);
		
		}
		
        $results = $query->hydrate(false)->all();	
        $data = $results->toArray();		
        // Once we have a result set we can get all the rows
		
        return $data;
    }


    /**
     * getDataByParams method     *
     * @param array $conditions The WHERE conditions for the Query. {DEFAULT : empty}
     * @param array $fields The Fields to SELECT from the Query. {DEFAULT : empty}
     * @return void
     */
    public function getDataByParams(array $fields, array $conditions){
        
		$options = [];
		
        if(!empty($fields))
           $options['fields']     = $fields;
        if(!empty($conditions))
           $options['conditions'] = $conditions;
	   
        $query = $this->find('all', $options);
		
        $results = $query->hydrate(false)->all();
		// Once we have a result set we can get all the rows
        $data = $results->toArray();
        return $data;

    }


    /**
     * deleteByIds method
     * @param array $ids it can be one or more to delete the timeperiod rows . {DEFAULT : null}
     * @return void
     */
    public function deleteByIds($ids = null){
        
		$result = $this->deleteAll(['TimePeriod_NId IN' => $ids]);

        return $result;
    }

        
    
	/**
     * deleteByParams method
     *
     * @param array $conditions on the basis of which record will be deleted . 
     * @return void
    */
    
	public function deleteByParams(array $conditions){
        
		$result = $this->deleteAll($conditions);

        return $result;
    }
	
	/**
    * 
	* deleteByTimePeriod method       
    * @param  $timeperiodvalue timeperiod which will be saved in database if exists  will be deleted. 
    * @return void
    *
	*/
		
	public function deleteByTimePeriod($timeperiodvalue){
	
		if(isset($timeperiodvalue) && !empty($timeperiodvalue)){        			 
        	//deleteentity  checks whether record exists or not 
			$deleteentity = $this->find()->where(['TimePeriod'=>$timeperiodvalue])->first();
			
			if(isset($deleteentity) &&  !empty($deleteentity)){  
			
				if($result = $this->delete($deleteentity)){
					$msg['success']       = 'Record deleted successfully!!';
				    return $msg;
				}else{
					return $msg['error'] = 'Error while deletion';  
				}			
			}else{                                   // Already exists
				    return $msg['error'] = 'Entity not found';				
			}
		}else{
				    return $msg['error'] = 'No time period value ';			
		}
	}// end of function 


	
    
	

    /**
    *  getDataByTimeperiod method
    *  @param $timeperiodvalue The value on which you will get details on basis of the timeperiodvalue. {DEFAULT : empty}
    *  @param  $periodicity is optional parameter {DEFAULT : empty}
    */
	 
    public function getDataByTimeperiod($timeperiodvalue,$periodicity='')
    {
        $options =[];
		
		if(isset($timeperiodvalue) && !empty($timeperiodvalue)) {
		    $options['conditions'] = array('TimePeriod'=>$timeperiodvalue);
		}	
		
		if(isset($periodicity) && !empty($periodicity)) {
		    $options['conditions'] = array('Periodicity'=>$periodicity);
		}	
	   
		if(isset($timeperiodvalue) && !empty($timeperiodvalue)) {
			
			$timperioddetails = $this->find('all',$options);
			$results = $timperioddetails->hydrate(false)->all();
			// Once we have a result set we can get all the rows
            $data = $results->toArray();
            

		}  
		
	     
		return $data;

    }
	
	
	/**
     * savesingleTimePeriod method 
     * @param  $timeperiodvalue timeperiod which will be saved in database if exists already nothing will be happened. {DEFAULT : empty}
     * @param  $Periodicity is optional if it is passed then fine else by default its value will be A {DEFAULT : A}
     * @return void
    */
		
	public function savesingleTimePeriod($timeperiodvalue,$Periodicity='A'){
	
		if(isset($timeperiodvalue) && !empty($timeperiodvalue)){            
			
			//numrows if numrows >0 then record already exists else insert new row
		    $numrows = $this->find()->where(['TimePeriod'=>$timeperiodvalue])->count();
			
			if(isset($numrows) &&  $numrows ==0){  // new record
			
				$data = $this->newEntity();
				$data->TimePeriod     = $timeperiodvalue;
				$numberofdays_dec     = cal_days_in_month(CAL_GREGORIAN, 12, date('Y')); // 31
                //echo "There were {$number} days in August 2003";
				$data->StartDate      = date('Y').'-01-01';
				$data->EndDate        = date('Y').'-12-'.$numberofdays_dec;
				$data->Periodicity    = $Periodicity;
				if($this->save($data)){
					 $msg['id']      = $TimePeriod_NId = $this->id;   // Record saved new id returned 
					 $msg['success'] = 'Record saved successfully!!';
					 return $msg;
				}else{
					 return $msg['error']='Error while saving details';  
				}			
			}else{                                   // Already exists
				     return  $msg['error']='Error while saving details';				
			}
		}else{
				     return $msg['error']='No time period value ';			
		}
	}// end of function 
	

	/**
     * insertData method
     * @param array $fieldsArray Fields to insert with their Data.
     * @return void
    */
	 
    public function insertData($fieldsArray = []){
        
		echo $timeperiodvalue = $fieldsArray['TimePeriod'];		
		
		if(isset($timeperiodvalue) && !empty($timeperiodvalue)){            
			
		//numrows if numrows >0 then record already exists else insert new row
		$numrows = $this->find()->where(['TimePeriod'=>$timeperiodvalue])->count();
		
		if(isset($numrows) &&  $numrows ==0){  // new record
		//Create New Entity
		$data = $this->newEntity();
		$data->TimePeriod     = $timeperiodvalue;
		$numberofdays_dec     = cal_days_in_month(CAL_GREGORIAN, 12, date('Y')); // 31
		//echo "There were {$number} days in August 2003";
		$data->StartDate      = date('Y').'-01-01';
		$data->EndDate        = date('Y').'-12-'.$numberofdays_dec;
		$data->Periodicity    = $fieldsArray['Periodicity'];
	
        //Create new row and Save the Data
        if($this->save($data)){
			 $id = $this->id;
             $msg['success'] = 'Record saved successfully!!';	
        } else {
             $msg['error']   = 'Error while saving details';  
        }		
		}else{
			 $msg['error']  = 'Timeperiod already exist ';	
		}
		
		}
		
     return $msg;        

    }
    
	
		
	

}