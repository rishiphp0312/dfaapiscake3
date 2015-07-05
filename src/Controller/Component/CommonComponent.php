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
class CommonComponent extends Component {

    public $MDatabaseConnections = '';
    public $MSystemConfirgurations = '';
    public $dbcon = '';
    public $Users = '';
    public $Roles = '';
    public $components = ['Auth'];

    public function initialize(array $config) {
        //parent::initialize($config);
        $this->MDatabaseConnections = TableRegistry::get('MDatabaseConnections');
        $this->MSystemConfirgurations = TableRegistry::get('MSystemConfirgurations');
        $this->Users = TableRegistry::get('Users');
        $this->Roles = TableRegistry::get('MRoles');
        
        
        //$this->AreaLevelObj = TableRegistry::get('DevInfoInterface.AreaLevel');
        // $this->MDatabaseConnections=
    }

    /*
      guid is function which returns gid
     */

    public function guid() {

        if (function_exists('com_create_guid')) {
            return com_create_guid();
        } else {
            mt_srand((double) microtime() * 10000); //optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45); // "-"
            //$uuid =// chr(123)// "{"
            $uuid = substr($charid, 0, 8) . $hyphen
                    . substr($charid, 8, 4) . $hyphen
                    . substr($charid, 12, 4) . $hyphen
                    . substr($charid, 16, 4) . $hyphen
                    . substr($charid, 20, 12);
            //.chr(125);// "}"
            return $uuid;
        }
    }

    /*
      Cleandata is function which returns the passed parameter after
      removing whitespace or unnecesary characters with clean data
     */

    public function cleandata($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }


    
    /*
     * 
     * Create database connection details 
     */

    public function createDatabasesConnection($data = array()) {
        return $this->MDatabaseConnections->insertData($data);
    }

    public function testConnection($connectionstring = null) {
        //Configure::write('debug', 0);
		//pr($connectionstring);die;
		//pr($connectionstring);
        $db_source='';
        $db_connection_name='';
        $db_host='';
        $db_password='';
        $db_login='';
        $db_database='';
        $db_port='';
		$connectionstringdata =[];
//pr($connectionstring);die;
        $connectionstring = json_decode($connectionstring,true);              
        //pr($connectionstring);
		//die;
	   if(isset($connectionstring[_DATABASE_CONNECTION_DEVINFO_DB_CONN])){
		    $connectionstringdata = json_decode($connectionstring[_DATABASE_CONNECTION_DEVINFO_DB_CONN],true);
			$db_source = trim($connectionstringdata['db_source']);
			$db_connection_name = trim($connectionstringdata['db_connection_name']);
			$db_host = trim($connectionstringdata['db_host']);
			$db_login = trim($connectionstringdata['db_login']);
			$db_password = trim($connectionstringdata['db_password']);
			$db_port = trim($connectionstringdata['db_port']);
			$db_database = trim($connectionstringdata['db_database']);

			$db_source = strtolower($db_source);
	   }
        



        $flags = array(
            \PDO::ATTR_PERSISTENT => false,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        ); 
        
        if ($db_source == 'mysql') {
            try {    
                $this->dbcon = new \PDO('mysql:host='.$db_host.';dbname='.$db_database,$db_login, $db_password, $flags);
                return true;
            }catch (\PDOException $e) { 
                return $e->getMessage();
            }
                
        } else {
            try {    
                $this->dbcon = new \PDO(
                    "sqlsrv:server={$db_host};Database={$db_database}", $db_login, $db_password, $flags
                );
                return true;
            }catch (\PDOException $e) {
                return $e->getMessage();
            }
        }
      
    }

    /*
       getDbDetails is used to get  the database details  with respect to passed database id
      @$dbId is used to pass the database id
     */

    public function getDbDetails($dbId = null) {

        $configIsDefDB = $this->MSystemConfirgurations->findByKey('DEVINFO_DBID');
        if ($configIsDefDB) {
            $databasedetails = $this->MDatabaseConnections->getDbNameByID($configIsDefDB);
        }
        return $databasedetails;
    }
	
     /*
      Function getDbNameByID is used for fetching the database information with respect to passed database id
      @$dbId is used to pass the database id
     */

    public function getDbNameByID($dbId) {
           
        $databasedetails = array();
    
        $databasedetails = $this->MDatabaseConnections->getDbNameByID($dbId);
       
        return $databasedetails;
    }
    
    /*
    Get List of Database as per the Users      
    */
    public function getDatabases() {

        $userId = $this->Auth->User('id');
        $roleId = $this->Auth->User('role_id');

        if ($roleId == _SUPERADMINROLEID) // for super admin acces to all databases            
            $returnDatabaseDetails = $this->MDatabaseConnections->getAllDatabases();
        else
            $returnDatabaseDetails = $this->getAlldatabaseAssignedUsers($userId);
           
        return $returnDatabaseDetails;
    }
	
    
    /*
    * Function deleteDatabase is used for deleting the database details
    * 
    */
    
    public function deleteDatabase($dbId,$userId) {
            
            return $databasedetails = $this->MDatabaseConnections->deleteDatabase($dbId,$userId);       
    }
	
    /*
	 function to get  the databases  associated to specific users 
	 $user_id the userId of user 
	*/
	
    public function getAlldatabaseAssignedUsers($userId){
           $data =array();
           $All_databases = $this->Users->find()->where(['id'=>$userId])->contain(['MDatabaseConnections'],true)->hydrate(false)->all()->toArray();
           $alldatabases = current($All_databases)['m_database_connections'];
           //pr($alldatabases);die;
           if (isset($alldatabases) && !empty($alldatabases)) {
            foreach ($alldatabases as $index => $valuedb) {
                
                $connectionObject = json_decode($valuedb[_DATABASE_CONNECTION_DEVINFO_DB_CONN], true);
                //pr($connectionObject);die;
               
                  if (isset($connectionObject['db_connection_name']) && !empty($connectionObject['db_connection_name']) && $valuedb[_DATABASE_CONNECTION_DEVINFO_DB_ARCHIVED]=='0') {
                    $data[$index]['id'] = $valuedb[_DATABASE_CONNECTION_DEVINFO_DB_ID];
                    $data[$index]['dbName'] = $connectionObject['db_connection_name'];
                }
            }
        }
        return $data;
    }
    
    
    
    

    /*
      uniqueConnection is used to check the uniqueness of database connection name
      @$dbConnectionName is used to pass the database Connection Name
     */

    public function uniqueConnection($dbConnectionName = null) {
        $databasedetails = $this->MDatabaseConnections->uniqueConnection($dbConnectionName);
        return $databasedetails;
    }
	
	/*
		function for sending activation link 
		@params $userId , $email  
	*/
	
	public function sendActivationLink($userId, $email){
		//$user_id=1;
		$encodedstring = base64_encode(_SALTPREFIX1.$userId._SALTPREFIX2);		
        $website_base_url= _WEBSITE_URL."#/UserActivation/$encodedstring" ;
        
        $subject = 'DFA Reset password Link';
        $message ="<div><a href='".$website_base_url."'>Click here  to reset your password  </a></div> ";
        $fromEmail = 'vpdwivedi@dataforall.com';
		
        $this->sendEmail($email, $fromEmail, $subject, $message, 'smtp');									
	}


    
	
				
								
    
    
    public function processFileUpload($file = null) {
        
        // Check Blank Calls
        if(!empty($files)){
            
            foreach($files as $fieldName => $fileDetails):
                
                // Check if file was uploaded via HTTP POST
                if (!is_uploaded_file($fileDetails['tmp_name'])) :
                    return FALSE;
                endif;
                
                $dest = WWW_ROOT . 'uploads' . DS . 'xls' . DS . $fileDetails['name'];
                
                // Upload File
                if (move_uploaded_file($fileDetails['tmp_name'], $dest)) :
                    $filePaths[] = $dest;   // Upload Successful
                else:
                    return FALSE;   // Upload Failed
                endif;
                
            endforeach;
            
            return $filePaths;
        }
        
        return FALSE;
    }



    /*
	function for send email
	*/
	public function sendEmail($toEmail, $fromEmail, $subject=null, $message=null, $type='smtp'){
		$return = false;
        try {
            if(!empty($toEmail) && !empty($fromEmail)) {
                ($type == 'smtp') ? $type = 'defaultsmtp' : $type = 'default';        
                $emailClass = new Email($type);
		        $result= $emailClass
                        ->emailFormat('html')
                        ->from([$fromEmail => $subject])
				        ->to($toEmail)
                        ->subject($subject)
                        ->send($message);
                if($result) {
                    $return = true;
                }
            }
        }
        catch(Exception $e) {
            $return = $e;            
        }
        
        return $return;
	}


    /*
	function to get role details
	*/
	public function getRoleDetails($roleId) {

        return $this->Roles->getRoleByID($roleId);
    }


}
