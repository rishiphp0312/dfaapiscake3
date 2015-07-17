<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\Database\Statement\PDOStatement;
use Cake\Core\Configure;
use Cake\Network\Email\Email;

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
    public $components = ['Auth', 'UserAccess'];

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
      function to check whether user has relation with db or not
     */

    public function checkUserDbRelation($userId = null, $dbId = null) {

        return $this->RUserDatabases->checkUserDbRelation($userId, $dbId);
    }

    /*
      function to update the  password while activating request
     */

    public function updatePassword($data = []) {

        return $this->Users->addModifyUser($data);
    }

    /*
      function to get the  users details on  passed conditions and fields
     */

    public function getDataByParams($conditions = [], $fields = []) {

        return $details = $this->Users->getDataByParams($conditions, $fields);
    }

    /*
      function to get list of all Roles
     */

    public function listAllRoles() {

        return $listAllRoles = $this->Roles->listAllRoles();
    }

    /*
      get userDatabase ID
      @userId represents user id
      @dbId   represents database id
     */

    public function getUserDatabaseId($userId, $dbId) {

        return $getidsRUD = $this->RUserDatabases->getUserDatabaseId($userId, $dbId);
    }

    /* 
	  getUserDatabasesRoles gives the roles of users 
      get the roles on basis of dbId  and userId 
     */

    public function getUserDatabasesRoles($userId = null, $dbId = null) {
        
		$rolesarray = [];
	
        $getidsRUD = $this->RUserDatabases->getUserDatabaseId($userId, $dbId);       				

		if ($getidsRUD) {
            $listAllRoleIDs = $this->RUserDatabasesRoles->getRoleIDsDatabase($getidsRUD); //index for rudrid  and value for  roleid
            
			foreach ($listAllRoleIDs as $index => $RoleId) {
                $rolesarray[] = $this->Roles->returnRoleValue($RoleId); //gives value of role on passed role id 
            }
        }
        return $rolesarray;
    }

    /*
      function to get listing of all users with their roles related to specific databases
     */

    public function listAllUsersDb($dbId = null) {

        $userRoles = [];
        $data = $this->MDatabaseConnections->listAllUsersDb($dbId); // get list of all users of dbId
        if (isset($data) && !empty($data)) {
            foreach ($data as $index => $value) {
                $userId = $value[_USER_ID];
                $roleIdsDb['roles'] = $this->getUserDatabasesRoles($userId, $dbId); //get roles of users of dbId

                $userRoles[$index] = $value;
                $userRoles[$index]['roles'] = $roleIdsDb['roles'];
            }
        }
        return $userRoles;
    }

    /*
     * deleteUserRolesAndDbs to delete the users 
     * $userId can be array multiple user ids 
     * $dbId is database id 
     */

    public function deleteUserRolesAndDbs($userId = [], $dbId = null) {

        if (!empty($dbId) && $dbId > 0) {
            if (isset($userId) && !empty($userId)) {
                $getidsRUD = $this->RUserDatabases->getUserDatabaseId($userId, $dbId);    // get RUD id
                $getidsRUDR = $this->RUserDatabasesRoles->getRoleIDsDatabase($getidsRUD); //  index for rudrid and value for roleid 
                $allRUDR_ids = array_keys($getidsRUDR); // all RUDR ids 
                if ($getidsRUD) {
                    $deleteDatabase = $this->RUserDatabases->deleteUserDatabase($getidsRUD); //delete db
                    if ($deleteDatabase > 0) {
                        $deleteRoleDatabase = $this->RUserDatabasesRoles->deleteUserRolesDatabase($getidsRUD); //delete roles
                        if ($deleteRoleDatabase > 0) {
                            if (count($allRUDR_ids) > 0) {
                                $deleteAreas = $this->UserAccess->deleteUserAreaAccess($getidsRUD, $allRUDR_ids); //delete areas
                                $deleteIndicators = $this->UserAccess->deleteUserIndicatorAccess($getidsRUD, $allRUDR_ids); //delete ind
                            }
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

    public function updateLastLoggedIn($fieldsArray = []) {
        $this->Users->updateLastLoggedIn($fieldsArray);
    }

    /*
      function to check the duplicate email
     * 
     */

    public function checkEmailExists($email = null, $userId = null) {
        return $getDetailsByEmail = $this->Users->checkEmailExists($email, $userId);
    }

    /*
     * deleteUserRoles
      function is  used for deleting roles while  modifying  user
     * $type E  is for status deleting existing roles which are not found in posted data 
     * $type F  is for case when existing roles are found in posted data
      $getIdsRUD is the user_database_id
     */

    public function deleteUserRoles($roledIds = [], $getIdsRUD = [], $type = null) {
        $deleteRoles = 0;

        if ($getIdsRUD) {
            if ($type == 'E')
                $deleteRoles = $this->RUserDatabasesRoles->deleteUserRoles($getIdsRUD, $roledIds, $type); // delete these $roledIds
            else
                $deleteRoles = $this->RUserDatabasesRoles->deleteUserRoles($getIdsRUD, $roledIds, $type); // delete not in these $roledIds
        }
        return $deleteRoles;
    }

    /*
     *
      function to add or modify the users with their databases and roles  respectively
     * 
     */

    public function addModifyUser($fieldsArray = [], $dbId = null) {
        if ($dbId > 0) {

            $updated_userid = $this->Users->addModifyUser($fieldsArray);  // update or insert user 

            if ($updated_userid) {

                if (isset($fieldsArray[_USER_ID]) && !empty($fieldsArray[_USER_ID])) {

                    $existRoles = $this->getUserDatabasesRoles($fieldsArray[_USER_ID], $dbId); //get existing roles 
					//pr($existRoles );die;
                    $resultarray_intersect = array_intersect($fieldsArray['roles'], $existRoles); // get the common roles between posted and  exists roles 
                    // getidsRUD stores the user_database_id value from r_user_databases table 
                    $getidsRUD = $this->RUserDatabases->getUserDatabaseId($fieldsArray[_USER_ID], $dbId);
                    $rolesid_array = array();

                    if (isset($resultarray_intersect) && count($resultarray_intersect) > 0) {
                        foreach ($resultarray_intersect as $index => $value) {
                            // getting role ids 					
                            $rolesid_array[] = $this->Roles->returnRoleId($value);
                        }
                    }

                    // case when posted data Roles is not found in existing  roles of user 
                    $rolesNotinPost = array();

                    if (empty($resultarray_intersect) && !empty($existRoles)) {
                        foreach ($existRoles as $index => $valueroles) {
                            $rolesNotinPost[] = $this->Roles->returnRoleId($valueroles);
                        }
                    }

                    if (isset($rolesNotinPost) && count($rolesNotinPost) > 0) {
                        $this->deleteUserRoles($rolesNotinPost, $getidsRUD, 'E');
                    }

                    //for not in delete of above role ids
                    if (isset($rolesid_array) && count($rolesid_array) > 0) {
                        $this->deleteUserRoles($rolesid_array, $getidsRUD, 'F');
                    }

                    $resultarray_difference = array_diff($fieldsArray['roles'], $existRoles);
                    $noof_roles = count($resultarray_difference);
                } else {

                    $resultarray_difference = $fieldsArray['roles'];
                    $noof_roles = count($resultarray_difference);
                }



                if (empty($fieldsArray[_USER_ID]) || empty($getidsRUD)) {
                    $fieldsArrayDB = [];
                    $fieldsArrayDB[_RUSERDB_USER_ID] = $updated_userid;
                    $fieldsArrayDB[_RUSERDB_DB_ID] = $dbId;
                    $fieldsArrayDB[_RUSERDB_CREATEDBY] = $this->Auth->User('id');
                    $fieldsArrayDB[_RUSERDB_MODIFIEDBY] = $this->Auth->User('id');
                    $lastinserted_userid_db = $this->RUserDatabases->addUserDatabases($fieldsArrayDB); // for saving user  db
                }else{
					$getidsRUDR = $this->RUserDatabasesRoles->getRoleIDsDatabase($getidsRUD); //  index for rudrid and value for roleid 
					$allRUDR_ids = array_keys($getidsRUDR); // all RUDR ids 
					$lastinserted_userid_db = $getidsRUD;
					//pr($getidsRUD);
					//pr($allRUDR_ids);die;
					$this->UserAccess->deleteUserAreaAccess($getidsRUD,$allRUDR_ids); //deleting existing areas 
					$this->UserAccess->deleteUserIndicatorAccess($lastinserted_userid_db,$allRUDR_ids); //deleting existing indicators 
				}
				
                $cnt = 0;
                // $resultarray_difference this will be empty if posted roles and existing roles both are same
                if (isset($resultarray_difference) && count($resultarray_difference) > 0) {
                    foreach ($resultarray_difference as $index => $value) {
                        // role ids which need  to be inserted  	
                        if (isset($fieldsArray[_USER_ID]) && !empty($fieldsArray[_USER_ID]) && !empty($getidsRUD)) {
                            $roleId = trim($this->Roles->returnRoleId($value));
                            $fieldsArrayRoles[_RUSERDBROLE_USER_DB_ID] = trim(current($getidsRUD));
                        } else {
                            $roleId = trim($this->Roles->returnRoleId($value));
                            $fieldsArrayRoles[_RUSERDBROLE_USER_DB_ID] = trim($lastinserted_userid_db);
                        }
                        $fieldsArrayRoles[_RUSERDBROLE_ROLE_ID] = $roleId;
                        $fieldsArrayRoles[_RUSERDBROLE_CREATEDBY] = $this->Auth->User('id');
                        $fieldsArrayRoles[_RUSERDBROLE_MODIFIEDBY] = $this->Auth->User('id');
                        $rolesAdded[] = $rudbrolesId = $this->RUserDatabasesRoles->addUserRoles($fieldsArrayRoles); //saving roles
                        
						//saving areas accessible for user
                        if (count($fieldsArray['areaid']) > 0) {
                            foreach ($fieldsArray['areaid'] as $value) {
                                $fieldsArrayAreas = [_RACCESSAREAS_AREA_ID => $value,
                                    _RACCESSAREAS_USER_DATABASE_ID => $lastinserted_userid_db,
                                    _RACCESSAREAS_USER_DATABASE_ROLE_ID => $rudbrolesId
                                ];
                                $this->UserAccess->createRecordAreaAccess($fieldsArrayAreas);
                            }
                        }

                        //saving indicators accessible for user 
                        if (count($fieldsArray['indGids']) > 0) {
                            foreach ($fieldsArray['indGids'] as $value) {
                                $fieldsArrayInd = [_RACCESSINDICATOR_INDICATOR_GID => $value,
                                    _RACCESSINDICATOR_USER_DATABASE_ID => $lastinserted_userid_db,
                                    _RACCESSINDICATOR_USER_DATABASE_ROLE_ID => $rudbrolesId
                                ];
                                $this->UserAccess->createRecordIndicatorAccess($fieldsArrayInd);
                            }
                        }


                        $cnt++;

                        $noof_roles;
                    }
                    if (count($rolesAdded) == $noof_roles) {
                        return $updated_userid;
                    } else {
                        return 0;
                    }
                }
                return $updated_userid; //case when no change in roles 
            }
        }
        return 0;
    }

    /*
     * 
     * function to return the autocomplete details 
     *
     */

    public function getAutoCompleteDetails() {
        return $this->Users->getAutoCompleteDetails();
    }

    /*
     * 
     * function to return the role id on basis of passed role value
     * @roleValue is  passed as roles  like 'ADMIN' or 'DATAENTRY'
     */

    public function returnRoleId($roleValue = null) {

        return $this->Roles->returnRoleId($roleValue);
    }

    /*
      function for sending activation link
      @params $userId , $email
     */

    public function sendActivationLink($userId, $email, $name) {

        $encodedstring = base64_encode(_SALTPREFIX1 . '-' . $userId . '-' . _SALTPREFIX2);
        $website_base_url = _WEBSITE_URL . "#/UserActivation/$encodedstring";
        $subject = 'DFA Data Admin Activation';
        $message = "<div>Dear " . ucfirst($name) . ",<br/>
			Please 	<a href='" . $website_base_url . "'>Click here  </a> to activate and setup your password.<br/><br/>
			Thank you.<br/>
			Regards,<br/>
			DFA Database Admin
			</div> ";

        $fromEmail = 'vpdwivedi@dataforall.com';
        $this->sendEmail($email, $fromEmail, $subject, $message, 'smtp');
    }

    /*
      function for sending notification on adding user to db
     */

    public function sendDbAddNotify($email, $name) {


        $subject = 'DFA Data Admin Database notification';
        $message = "<div>Dear " . ucfirst($name) . ",<br/>
                    You have been successfully added to new database .<br/><br/>
                    Thank you.<br/>
                    Regards,<br/>
                    DFA Database Admin
                    </div> ";
        $fromEmail = 'vpdwivedi@dataforall.com';
        $this->sendEmail($email, $fromEmail, $subject, $message, 'smtp');
    }

    /*
      function for send email
     */

    public function sendEmail($toEmail, $fromEmail, $subject = null, $message = null, $type = 'smtp') {
        $return = false;
        try {
            if (!empty($toEmail) && !empty($fromEmail)) {
                ($type == 'smtp') ? $type = 'defaultsmtp' : $type = 'default';
                $emailClass = new Email($type);
                $result = $emailClass->emailFormat('html')->from([$fromEmail => $subject])->to($toEmail)->subject($subject)->send($message);
                if ($result) {
                    $return = true;
                }
            }
        } catch (Exception $e) {
            $return = $e;
        }

        return $return;
    }

    /*
      function to send re-activation link to user
     */

    public function resetPassword($userId = null) {

        $return = array('status' => true, 'error' => '');

        if (!empty($userId)) {
            // get User details
            $userData = $this->getUserDetails($userId);

            if ($userData) {
                // update user status field as 0 (in-active)
                $fieldsArray = [
                    _USER_ID => $userId,
                    _USER_STATUS => 0,
                    _USER_MODIFIEDBY => $this->Auth->User('id')
                ];
                $conditions = [
                    _USER_ID => $userId
                ];
                $this->Users->updateDataByParams($fieldsArray, $conditions);

                // Send mail to activate the account and setup the password
                $this->sendActivationLink($userId, $userData['email'], $userData['name']);
            }
        }

        return $return;
    }

    /*
      function to get User details
     */

    public function getUserDetails($userId = null) {

        $data = [];
        if (!empty($userId)) {
            $fieldsArray = [
                _USER_ID,
                _USER_NAME,
                _USER_EMAIL,
                _USER_STATUS
            ];
            $conditionArray = [
                _USER_ID => $userId
            ];

            $dt = $this->getDataByParams($fieldsArray, $conditionArray);
            if (isset($dt[0]))
                $data = $dt[0];
        }
        return $data;
    }

    /*
     * 
     * function to return the role id on basis of passed role value
     * @roleValue is  passed as roles  like 'ADMIN' or 'DATAENTRY'
     */

    public function getDbRolesDetails($fields = [], $conditions = [], $type = 'all', $extra = []) {
        return $this->RUserDatabasesRoles->getDetails($fields, $conditions, $type, $extra);
    }

}
