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
	
	   echo  $Subgroup_Type_Name = $fieldsArray['Subgroup_Type_Name'];		
		
		if(isset($Subgroup_Type_Name) && !empty($Subgroup_Type_Name)){            
			
			//numrows if numrows >0 then record already exists else insert new row
		    echo $numrows = $this->find()->where(['Subgroup_Type_Name'=>$Subgroup_Type_Name])->count();
		
			if(isset($numrows) &&  $numrows ==0){  // new record
			   
			    //$options           = array();
				//$options['fields'] = array('MAX(Subgroup_Type_Order)');
			    //$conditions['fields'] = array('Subgroup_Type_Order');
				$query = $this->find();
				$results = $query->hydrate(false)->select(['max' => $query->func()->max('Subgroup_Type_Order')]);

				//pr($results);			
								//$query =  $this->find('all',$options) ;
				//pr($query);die;
				$results = $results->all();
				pr($results);die;				


               // Once we have a result set we can get all the rows
                $maxordervalue = $results->toArray();
				pr($maxordervalue);die;
				echo $maxvalue      = $maxordervalue['maxvalue'];
					die('hua');
				$data = $this->newEntity();
				
				$data->Subgroup_Type_GID     = $fieldsArray['Subgroup_Type_GID'];		
				$data->Subgroup_Type_Name    = $fieldsArray['Subgroup_Type_Name'];
				$data->Subgroup_Type_Global  = (isset($fieldsArray['Subgroup_Type_Global']) && !empty($fieldsArray['Subgroup_Type_Global']))?$fieldsArray['Subgroup_Type_Global']:0;
				$data->Subgroup_Type_Order  = (isset($fieldsArray['Subgroup_Type_Order']) && !empty($fieldsArray['Subgroup_Type_Order']))?$fieldsArray['Subgroup_Type_Order']:($maxvalue+1);
				
				if($this->save($data)){
					 $msg['success'] = 'Record saved successfully!!'.pr($maxordervalue);die;
					 return $msg;
				}else{
					 return $msg['error']='Error while saving details';  
				}			
			}else{                                   // Already exists
				     return  $msg['error']='Record Already exists!!';				
			}
		}else{
				     return $msg['error']='No time period value ';			
		}
	}// end of function 
	


}