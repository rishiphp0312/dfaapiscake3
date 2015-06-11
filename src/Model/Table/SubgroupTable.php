<?php  
namespace App\Model\Table;

use App\Model\Entity\Subgroup;
use Cake\ORM\Table;


/**
 * SubgroupTable Model
 */
class SubgroupTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('UT_Subgroup_en');
        $this->primaryKey('Subgroup_NId');
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
		
        if(isset($ids) && !empty($ids))
        $options['conditions'] = ['Subgroup_NId IN'=>$ids];
	    
		if(isset($fields) && !empty($fields))
         $options['fields'] = $fields;	    		
        
		if($type=='list'){
			 $options['keyField']   = $fields[0];	    		
             $options['valueField'] = $fields[1];	  
  		     $query = $this->find($type, $options);
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
    *  getDataBySubgroupName method
    *  @param $SubgroupName The value on which you will get details on basis of  the Subgroup. {DEFAULT : empty}
    *  @return  array
    */
	 
    public function getDataBySubgroupName($SubgroupName)
    {        
	    $Subgroup_Namedetails =array();
		if(!empty($SubgroupName))       
		$Subgroup_Namedetails = $this->find('all')->where(['Subgroup_Name'=>$SubgroupName])->hydrate(false)->first();
	   		   
		return $Subgroup_Namedetails;
    }
	
	
	

	
		
	/**
    * 
	* deletesingleSubgroup method       
    * @param  $Subgroupvalue contains  Subgroup name  which will be deleted from database if exists 
    * @return void
    *
	*/
		
	public function deletesingleSubgroup($Subgroupvalue){
	
		if(isset($Subgroupvalue) && !empty($Subgroupvalue)){            
	
        	//deleteentity  checks whether record exists or not 
		    $deleteentity = $this->find()->where(['Subgroup_Name'=>$Subgroupvalue])->first();
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
    * 
	* insertData method       
    * @param  $fieldsArray contains  posted data 
    * @return void
    *
	*/
	
	public function insertData($fieldsArray){
	
	    $Subgroup_Name = $fieldsArray['Subgroup_Name'];		
	    $Subgroup_Type = $fieldsArray['Subgroup_Type'];		
		
		if(isset($Subgroup_Name) && !empty($Subgroup_Name)){            
			
			//numrows if numrows >0 then record already exists else insert new row
		    $numrows = $this->find()->where(['Subgroup_Name'=>$Subgroup_Name])->count();
		
			if(isset($numrows) &&  $numrows ==0){  // new record			   
				
				if(empty($fieldsArray['Subgroup_Order'])){
					
					$query         = $this->find();
					$results       = $query->select(['max' => $query->func()->max('Subgroup_Order')])->first();
					$ordervalue    = $results->max;
					$maxordervalue = $ordervalue+1;
					$fieldsArray['Subgroup_Order'] = $maxordervalue;	
				}
			   
                //Create New Entity
                $Subgroup = $this->newEntity();
				pr( $Subgroup);
                //Update New Entity Object with data
                $Subgroup = $this->patchEntity($Subgroup, $fieldsArray);
				
				if ($this->save($Subgroup)) {
					$msg['success'] = 'Record saved successfully!!';
				}else{
				    $msg['error']   = 'Error while saving details';  
				}
			
			}else{         // Subgroup Already exists
				    $msg['error']   = 'Record Already exists!!';				
			}
		}else{
				    $msg['error']   = 'No time period value ';			
		}
         return $msg;		
	}// end of function 
	

}