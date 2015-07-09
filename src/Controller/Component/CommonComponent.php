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
        parent::initialize($config);
        $this->MDatabaseConnections = TableRegistry::get('MDatabaseConnections');
        $this->MSystemConfirgurations = TableRegistry::get('MSystemConfirgurations');
        $this->Users = TableRegistry::get('Users');
        $this->Roles = TableRegistry::get('MRoles');
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
     * 
     * Create database connection details
     * @$data passed as array
     */

    public function createDatabasesConnection($data = array()) {
        return $this->MDatabaseConnections->insertData($data);
    }
	

    /*
     * 
     * check the database connection  
     */

    public function testConnection($connectionstring = null) {

        $db_source = '';
        $db_connection_name = '';
        $db_host = '';
        $db_password = '';
        $db_login = '';
        $db_database = '';
        $db_port = '';
        $connectionstringdata = [];
        $connectionstring = json_decode($connectionstring, true);

        if (isset($connectionstring[_DATABASE_CONNECTION_DEVINFO_DB_CONN])) {

            $connectionstringData = json_decode($connectionstring[_DATABASE_CONNECTION_DEVINFO_DB_CONN], true);
            $db_source = trim($connectionstringData['db_source']);
            $db_connection_name = trim($connectionstringData['db_connection_name']);
            $db_host = trim($connectionstringData['db_host']);
            $db_login = trim($connectionstringData['db_login']);
            $db_password = trim($connectionstringData['db_password']);
            $db_port = trim($connectionstringData['db_port']);
            $db_database = trim($connectionstringData['db_database']);

            $db_source = strtolower($db_source);
        }




        $flags = array(
            \PDO::ATTR_PERSISTENT => false,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        );

        if ($db_source == 'mysql') {
            try {
                $this->dbcon = new \PDO('mysql:host=' . $db_host . ';dbname=' . $db_database, $db_login, $db_password, $flags);
                return true;
            } catch (\PDOException $e) {
                return $e->getMessage();
            }
        } else {
            try {
                $this->dbcon = new \PDO(
                        "sqlsrv:server={$db_host};Database={$db_database}", $db_login, $db_password, $flags
                );
                return true;
            } catch (\PDOException $e) {
                return $e->getMessage();
            }
        }
    }
	
	
	/*
      Function getDbDetails is to get  the database information with respect to passed database id
      @$dbId is used to pass the database id
     */

    public function getDbConnectionDetails($dbId) {

        $databasedetails = array();

        $databasedetails = $this->MDatabaseConnections->getDbConnectionDetails($dbId);

        return $databasedetails;
    }

    /*
      Function getDbNameByID is to get  the database information with respect to passed database id
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

    public function deleteDatabase($dbId, $userId) {

        return $databasedetails = $this->MDatabaseConnections->deleteDatabase($dbId, $userId);
    }

    /*
      function to get  the databases  associated to specific users
      $userId the user Id of user
     */

    public function getAlldatabaseAssignedUsers($userId) {
        $data = array();
        $All_databases = $this->Users->find()->where(['id' => $userId])->contain(['MDatabaseConnections'], true)->hydrate(false)->all()->toArray();
        $alldatabases = current($All_databases)['m_database_connections'];
        if (isset($alldatabases) && !empty($alldatabases)) {
            foreach ($alldatabases as $index => $valuedb) {

                $connectionObject = json_decode($valuedb[_DATABASE_CONNECTION_DEVINFO_DB_CONN], true);

                if (isset($connectionObject['db_connection_name']) && !empty($connectionObject['db_connection_name']) && $valuedb[_DATABASE_CONNECTION_DEVINFO_DB_ARCHIVED] == '0') {
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

    public function uniqueConnection($dbConnectionName) {
        $databasedetails = $this->MDatabaseConnections->uniqueConnection($dbConnectionName);
        return $databasedetails;
    }

    /*
      function to check activation link is used or not
      @params $userId , $email
     */

    public function checkActivationLink($userId) {
        $status = $this->Users->checkActivationLink($userId);
        return $status;
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
     * Get mime Types List
     * 
     * @param array $allowedExtensions Allowed extensions
     * @return Mime Types array
     */
    public function mimeTypes($allowedExtensions = []) {
        $mimeTypes  = [
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];
        
        $allowedExtensionsMimeTypes = array_intersect_key($mimeTypes, array_flip($allowedExtensions));
        
        return $allowedExtensionsMimeTypes;
    }
								
    
    /*
     * Process File uploads
     * 
     * @param array $files POST $_FILES Variable
     * @param array $extensions Valid extension allowed 
     * @return uploaded filename
     */
    public function processFileUpload($files = null, $allowedExtensions = [], $extra = []) {

        // Check Blank Calls
        if (!empty($files)) {

            foreach ($files as $fieldName => $fileDetails):

                // Check if file was uploaded via HTTP POST
                if (!is_uploaded_file($fileDetails['tmp_name'])) :
                    return ['error' => 'File uploaded via unaccepted method.'];
                endif;

                $dest = _XLS_PATH . DS . $fileDetails['name'];
                
                $mimeType = $fileDetails['type'];
                if( !in_array($mimeType, $this->mimeTypes($allowedExtensions)) ){
                    return ['error' => 'Invalid file.'];
                }
                
                // Upload File
               // 
                if (move_uploaded_file($fileDetails['tmp_name'], $dest)) :
                    if(isset($extra['createLog']) && $extra['createLog'] == true){
                        $pathinfo = pathinfo($fileDetails['name']);
                        $copyDest = _LOGS_PATH . DS . md5(time()). '.' . $pathinfo['extension'];
                        if (!@copy($dest, $copyDest)){
                            return ['error' => 'File upload failed.'];
                        }
                        define('_LOGPATH', $copyDest);
                    }
                    $filePaths[] = $dest;   // Upload Successful
                
                else:
                    return ['error' => 'File upload failed.'];   // Upload Failed
                endif;

            endforeach;

            return $filePaths;
        }
        return ['error' => 'This location cannot be accessed.'];
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
      function to get role details
     */

    public function getRoleDetails($roleId) {

        return $this->Roles->getRoleByID($roleId);
    }

}
