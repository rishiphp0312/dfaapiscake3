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
    *  getDataBySubgroupTypeName method
    *  @param $Subgroup_Type_Name The value on which you will get details on basis of  the Subgroup type name. {DEFAULT : empty}
    *  @return  array
    */
	 
    public function getDataBySubgroupTypeName($Subgroup_Type_Name)
    {        
		if(!empty($Subgroup_Type_Name))       
		$Subgroup_Namedetails = $this->find('all')->where(['Subgroup_Type_Name'=>$Subgroup_Type_Name])->all()->toArray();
	    else
		$Subgroup_Namedetails = $this->find()->where()->all()->toArray();        		   
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
			   
				$query         = $this->find();
				$results       = $query->select(['max' => $query->func()->max('Subgroup_Type_Order')])->first();
				$ordervalue    = $results->max;
				$maxordervalue = $ordervalue+1;
				
				if(isset($maxordervalue) && !empty($maxordervalue))
				$fieldsArray['Subgroup_Type_Order'] = $maxordervalue;
			     else
			    $fieldsArray['Subgroup_Type_Order'] = '';				
				
                //Create New Entity
                $Subgroup_Type = $this->newEntity();

                //Update New Entity Object with data
                $Subgroup_Type = $this->patchEntity($Subgroup_Type, $fieldsArray);
				pr($Subgroup_Type);
				die;
				
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