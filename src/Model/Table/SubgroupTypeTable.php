<?php  
namespace App\Model\Table;

use App\Model\Entity\SubgroupType;
use Cake\ORM\Table;


/**
 * SubgroupTypeTable Model
 */
class SubgroupTypeTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('UT_Subgroup_Type_en');
        $this->primaryKey('Subgroup_Type_NId');
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
        $options['conditions'] = ['Subgroup_Type_NId IN'=>$ids];
	    
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
    *  getDataBySubgroupTypeName method
    *  @param $Subgroup_Type_Name The value on which you will get all details corresponding to the  Subgroup type name. {DEFAULT : empty}
    *  @return  array
    */
	 
    public function getDataBySubgroupTypeName($Subgroup_Type_Name)
    {   
	    $Subgroup_Namedetails=array();  
		
		if(!empty($Subgroup_Type_Name))       
		$Subgroup_Namedetails = $this->find('all')->where(['Subgroup_Type_Name'=>$Subgroup_Type_Name])->hydrate(false)->first();
	    		   
		return $Subgroup_Namedetails;
    }
	
	
	
		
	/**
    * 
	* deletesingleSubgroupType method       
    * @param  $Subgroup_Type_Name contains  Subgroup type  name  which will be deleted from database if exists 
    * @return void
    *
	*/
		
	public function deletesingleSubgroupType($Subgroup_Type_Name){
	
		if(isset($Subgroup_Type_Name) && !empty($Subgroup_Type_Name)){            
	
        	//deleteentity  checks whether record exists or not 
		    $deleteentity = $this->find()->where(['Subgroup_Type_Name'=>$Subgroup_Type_Name])->first();
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
     * insertData  method 
       @return void
    */
		
	public function insertData($fieldsArray){
	
	    $Subgroup_Type_Name = $fieldsArray['Subgroup_Type_Name'];		
		
		if(isset($Subgroup_Type_Name) && !empty($Subgroup_Type_Name)){            
			
			//numrows if numrows >0 then record already exists else insert new row
		    $numrows = $this->find()->where(['Subgroup_Type_Name'=>$Subgroup_Type_Name])->count();
		
			if(isset($numrows) &&  $numrows ==0){  // new record
			
				if(empty($fieldsArray['Subgroup_Type_Order'])){
					
				  $query         = $this->find();
				  $results       = $query->select(['max' => $query->func()->max('Subgroup_Type_Order')])->first();
				  $ordervalue    = $results->max;
				  $maxordervalue = $ordervalue+1;
				  $fieldsArray['Subgroup_Type_Order'] = $maxordervalue;	
				}
				
                //Create New Entity
                $Subgroup_Type = $this->newEntity();

                //Update New Entity Object with data
                $Subgroup_Type = $this->patchEntity($Subgroup_Type, $fieldsArray);
				
				if ($this->save($Subgroup_Type)) {
					$msg['success'] = 'Record saved successfully!!';
				}else{
				    $msg['error']   = 'Error while saving details';  
				}
			}else{         // Subgroup_Type_Name Already exists
				    $msg['error']   = 'Record Already exists!!';				
			}
		}else{
				    $msg['error']   = 'No time period value ';			
		}
         return $msg;		
	}// end of function 
	


}