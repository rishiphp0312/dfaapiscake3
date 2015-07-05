<?php
namespace App\Controller\Component;
use Cake\Controller\Component;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\Database\Statement\PDOStatement;
use Cake\Core\Configure;

/**
 * Common period Component
 */
class UserCommonComponent extends Component {

    public $MDatabaseConnections = '';
    public $MSystemConfirgurations = '';
    public $dbcon = '';
    public $Users = '';
    public $Roles = '';
    public $RUserDatabases = '';
    public $RUserDatabasesRoles = '';
    
    
    public $components = ['Auth'];

    public function initialize(array $config) {
        //parent::initialize($config);
        $this->MDatabaseConnections = TableRegistry::get('MDatabaseConnections');
        $this->MSystemConfirgurations = TableRegistry::get('MSystemConfirgurations');
        $this->Users = TableRegistry::get('Users');
        $this->Roles = TableRegistry::get('MRoles');
        $this->RUserDatabases = TableRegistry::get('RUserDatabases');
        $this->RUserDatabasesRoles = TableRegistry::get('RUserDatabasesRoles');
		 $this->Auth->allow();
        
     
      
    }

   
    /*
      Cleandata is function which returns the passed parameter after
      removing whitespace or unnecesary characters with clean data
      mysql_real_escape_string($user)
     */

    public function cleandata($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
	
	/*
      function to update the  password while activating request 
    */
	
	public function updatePassword($data=[]){
		
		return $this->Users->addModifyUser($data);
	}
 
	/*
      function to get the  users details on  passed conditions and fields 
    */
   
 
    public  function getDataByParams($conditions=[],$fields=[]){
		return  $details = $this->Users->getDataByParams($conditions,$fields);
	}
   
    /*
      function to get listing all Roles 
    */
   
    public function listAllRoles(){

        return $listAllRoles = $this->Roles->listAllRoles();
    }
	
    /*
	Function to get the roles on basis of passed db id and user id
	*/
    function findUserDatabasesRoles($userId=null,$dbId=null){
        $rolesarray=[];
        $getidsRUD = $this->RUserDatabases->findUserDatabases($userId,$dbId);
        //$roleslist = $this->Roles->find('all')->combine(_DATABASE_ROLE_ID,_DATABASE_ROLE)->toArray();
            if($getidsRUD){
                  $listAllRoleIDs = $this->RUserDatabasesRoles->findRoleIDDatabase($getidsRUD);
                  foreach($listAllRoleIDs as $index=>$valueRoleids){                       
                    //$rolesarray[]=  $roleslist[$valueRoleids];                    
                    $rolesarray[]=   $this->Roles->returnRoleValue($valueRoleids);                    
                  }
            }
            return $rolesarray;
    }
    
    /*
      function to get listing of  all users with their roles related to specific databases 
    */
   
    public function listAllUsersDb($dbId=null){
         
		 $userRoles =[];
         $data = $this->MDatabaseConnections->listAllUsersDb($dbId);
         if(isset($data) && !empty($data )){
             foreach($data as $index=>$value){
				 $userId = $value[_USER_ID];
				 $roleIdsDb['roles'] = $this->findUserDatabasesRoles($userId,$dbId);  
				
				 $userRoles[$index] = $value;
				 $userRoles[$index]['roles']=$roleIdsDb['roles'];
          }
         }
         return $userRoles;
    }
    
    
    /*
     * function to delete the users 
     * $userId can be array multiple user ids 
     * $dbId is database id 
     */
    public function deleteUserRolesAndDbs($userId=[],$dbId=null){
        
        if(!empty($dbId) && $dbId>0){
            if(isset($userId) && !empty($userId)){
            $getidsRUD = $this->RUserDatabases->findUserDatabases($userId,$dbId);//
            if($getidsRUD){
                 $deleteDatabase = $this->RUserDatabases->deleteUserDatabase($getidsRUD);
                 if($deleteDatabase>0){                     
                      $deleteRoleDatabase = $this->RUserDatabasesRoles->deleteUserRolesDatabase($getidsRUD);
                       if($deleteRoleDatabase>0){
                           return $deleteRoleDatabase;
                       }
                 }                
            }
         }
        }
         return 0;
    }
    
	
    /*
      function to  update the users last login time 
     * 
     */   
	public function updateLastLoggedIn($fieldsArray = [])
    {   
		$this->Users->updateLastLoggedIn($fieldsArray);
	}
	
	/*
      function to check the duplicate email
     * 
     */  
	public function checkEmailExists($email=null,$userId=null)
    {
		 return $getDetailsByEmail = $this->Users->checkEmailExists($email,$userId);
	}
	
	
	
	
	/*
     * deleteUserRoles
	 function is  used for deleting roles while  modifying  user 
     * $type E  is for status deleting existing roles which are not found in posted data 
	 * $type F  is for case when existing roles are  found in posted data
       $getIdsRUD is the user_database_id 	 
     */  
	
	public function deleteUserRoles($roledIds=[],$getIdsRUD=[],$type=null)
    {
		$deleteRoles=0;
		    
		if($getIdsRUD){
			if($type=='E')
			$deleteRoles = $this->RUserDatabasesRoles->deleteUserRoles($getIdsRUD,$roledIds,$type); // delete these $roledIds
			else		
			$deleteRoles = $this->RUserDatabasesRoles->deleteUserRoles($getIdsRUD,$roledIds,$type); // delete not in these $roledIds
		}
		return $deleteRoles;
	}
	
	
	/*
     *
	 function to add or modify the users with their databases and roles  respectively
     * 
     */  
	 
    public function addModifyUser($fieldsArray = [],$dbId=null)
    {
        if($dbId>0){
		$details = $this->checkEmailExists($fieldsArray[_USER_EMAIL],$fieldsArray[_USER_ID]);
		
		if($details>0){ 												// email already exists 		
			return 0;
		}else{
			$updated_userid = $this->Users->addModifyUser($fieldsArray);  // update or insert user modifyUser

			if($updated_userid){
																									
			$roleslist = $this->Roles->find('all')->combine(_DATABASE_ROLE,_DATABASE_ROLE_ID)->toArray(); //get all roles list role as  index and roleid as value  	
			
			if(isset($fieldsArray[_USER_ID]) && !empty($fieldsArray[_USER_ID])){
			  
			  $existRoles    = $this->findUserDatabasesRoles($fieldsArray[_USER_ID],$dbId);	//get existing roles 
			 
			  $resultarray_intersect = array_intersect($fieldsArray['roles'],$existRoles);// get the common roles between posted and  exists roles 
			  
			  // getidsRUD stores the user_database_id value from r_user_databases table 
			  $getidsRUD = $this->RUserDatabases->findUserDatabases($fieldsArray[_USER_ID],$dbId); 
	      	  $rolesid_array = array();	
			  
			    if(isset($resultarray_intersect)&& count($resultarray_intersect)>0){
					foreach($resultarray_intersect as $index=>$value){
						$rolesid_array[]= $roleslist[$value]; 				// role ids which are common don't need  to be deleted 					
					}
			    }
				
				// case when posted data Roles is not found in existing  roles of user 
			   $rolesNotinPost = array();		//exist_rolesid_array_notinpost		  
				
			   if(empty($resultarray_intersect) && !empty($existRoles)){
				  foreach($existRoles as $index=>$valueroles){				
						  $rolesNotinPost[]= $roleslist[$valueroles];				
				  }
			   } 
			   //echo 'Not found in rolesNotinPost';
			   //echo 'Not found in rolesNotinPost';
			  
				if(isset($rolesNotinPost) && count($rolesNotinPost)>0){				
					$this->deleteUserRoles($rolesNotinPost,$getidsRUD,'E');
					
				}
				
				//echo 'before rolesid_array delete';
				//pr($rolesid_array);
					//echo 'before exist_rolesid_array_notinpostdelete';
				//pr($rolesNotinPost);
				//for not in delete of above role ids
				if(isset($rolesid_array) && count($rolesid_array)>0){				
					$this->deleteUserRoles($rolesid_array,$getidsRUD,'F');
				
				}
				
				$resultarray_difference = array_diff($fieldsArray['roles'],$existRoles); 			
				$noof_roles=count($resultarray_difference);
				
			}else{
				
				$resultarray_difference = $fieldsArray['roles'];
				$noof_roles=count($resultarray_difference);
			}
			//echo 'differ';
				//pr($resultarray_difference);
			// ids which needs to be inserted for roles 
			

			if(empty($fieldsArray[_USER_ID]) || empty($getidsRUD)){	
				
				$fieldsArrayDB = [];
				$fieldsArrayDB[_RUSERDB_USER_ID]   = $updated_userid;
				$fieldsArrayDB[_RUSERDB_DB_ID]     = $dbId;
				$fieldsArrayDB[_RUSERDB_CREATEDBY] = $this->Auth->User('id');
				$fieldsArrayDB[_RUSERDB_MODIFIEDBY]= $this->Auth->User('id');
				$lastinserted_userid_db = $this->RUserDatabases->addUserDatabases($fieldsArrayDB); // for saving user  db
							
			}
			$cnt=0;	
			if(isset($resultarray_difference)&& count($resultarray_difference)>0){
				foreach($resultarray_difference as $index=>$value){				   	    
						// role ids which need  to be inserted  	
						if(isset($fieldsArray[_USER_ID]) && !empty($fieldsArray[_USER_ID]) && !empty($getidsRUD))	{
							$roleId =  trim($roleslist[$value]);
							$fieldsArrayRoles[_RUSERDBROLE_USER_DB_ID] = trim(current($getidsRUD));							
						}else{
							$roleId = $this->returnRoleId($value);   
							$fieldsArrayRoles[_RUSERDBROLE_USER_DB_ID] = trim($lastinserted_userid_db);	
									
						}
						$fieldsArrayRoles[_RUSERDBROLE_ROLE_ID]    = $roleId;                       
                        $fieldsArrayRoles[_RUSERDBROLE_CREATEDBY]  = $this->Auth->User('id');
                        $fieldsArrayRoles[_RUSERDBROLE_MODIFIEDBY] = $this->Auth->User('id');
                        $resultid = $this->RUserDatabasesRoles->addUserRoles($fieldsArrayRoles);	
			    		$cnt++;
						
						$noof_roles;
						if($cnt==$noof_roles){
							return $resultid;
						}					
				}
				if($cnt!=$noof_roles){
					return 0;
				}	
			}
			  return $updated_userid;		
		}
		return 0;
			
		}
		}else{
			return 0;
		}	
	}
	
	
		
	/*
     * 
     * function to return the autocomplete details 
     *
     */
	 
	public function getAutoCompleteDetails(){
		 return $this->Users->getAutoCompleteDetails();
	}
    
    /*
     * 
     * function to return the role id on basis of passed role value
     * @roleValue is  passed as roles  like 'ADMIN' or 'DATAENTRY'
    */
    
	public function returnRoleId($roleValue=null){
		
		return $this->Roles->returnRoleId($roleValue);
    }
    


}
