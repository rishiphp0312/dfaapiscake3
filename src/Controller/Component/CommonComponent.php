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
class CommonComponent extends Component {

    public $MDatabaseConnections = '';
    public $MSystemConfirgurations = '';
    public $dbcon = '';
    public $Users = '';
    public $components = ['Auth'];

    public function initialize(array $config) {
        //parent::initialize($config);
        $this->MDatabaseConnections = TableRegistry::get('MDatabaseConnections');
        $this->MSystemConfirgurations = TableRegistry::get('MSystemConfirgurations');
        $this->Users = TableRegistry::get('Users');
        
        
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
      mysql_real_escape_string($user)
     */

    public function cleandata($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    /*
      Cleandata is function which returns the passed parameter after
      removing whitespace or unnecesary characters with clean data
      mysql_real_escape_string($user)
     */

    public function saveData($data) {
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
        Configure::write('debug', 0);
        
        $connectionstring = json_decode($connectionstring,true);
        //pr($connectionstring[_DATABASE_CONNECTION_DEVINFO_DB_CONN]);
        $connectionstringdata = json_decode($connectionstring[_DATABASE_CONNECTION_DEVINFO_DB_CONN],true);
        
        $db_source = trim($connectionstringdata['db_source']);
        $db_connection_name = trim($connectionstringdata['db_connection_name']);
        $db_host = trim($connectionstringdata['db_host']);
        $db_login = trim($connectionstringdata['db_login']);
        $db_password = trim($connectionstringdata['db_password']);
        $db_port = trim($connectionstringdata['db_port']);
        $db_database = trim($connectionstringdata['db_database']);
//        pr($connectionstring);die;

        $db_source = strtolower($db_source);



        $flags = array(
            \PDO::ATTR_PERSISTENT => false,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        );
        
            if ($db_source == 'mysql') {
                try {    
                    $this->dbcon = new \PDO('mysql:host='.$db_host.';dbname='.$db_database,$db_login, $db_password, $flags);
                }catch (\PDOException $e) {
                     return $e->getMessage();
                 }
                return 'success';
            } else {
                try {    
                  $this->dbcon = new \PDO(
                        "sqlsrv:server={$db_host};Database={$db_database}", $db_login, $db_password, $flags
                  );
                  return 'success';
               }catch (\PDOException $e) {
                     return $e->getMessage();
                }
            }
       

        /* try {

          $data = array();
          $dbh = new \PDO($db_source . ':host=' . $db_host . ';dbname=' . $db_database, $db_login, $db_password);
          if (!is_object($dbh)) {
          $data = false;
          }
          new PDO("sqlsrv:server=[sqlservername];Database=[sqlserverdbname]",  "[username]", "[password]");
          $data = true;
          return $data;
          } catch (PDOException $e) {
          // print "Error!: " . $e->getMessage() . "<br/>";
          $data = false;
          return $data;
          //die();
          } */
    }

    /*
      Function getDbDetails is used for fetching the database information with respect to passed database id
      @$dbId is used to pass the database id
     */

    public function getDbDetails($dbId = null) {

        $configIsDefDB = $this->MSystemConfirgurations->findByKey('DEVINFO_DBID');
        if ($configIsDefDB) {
            $this->MDatabaseConnections = TableRegistry::get('MDatabaseConnections');
            $databasedetails = $this->MDatabaseConnections->getDbNameByID($configIsDefDB);
        }
        return $databasedetails;
    }
    
    /*
      Function getAllDatabases is used for fetching the database details with respect to passed user logged in 
      
     */

    
    public function getAllDatabases($user_id) {
            
            return $databasedetails = $this->MDatabaseConnections->getAllDatabases($user_id);
        
        
    }
    
    /*
     * Function deleteDatabase is used for fetching the database details with 
     * respect to passed user logged in 
     * 
     */

    
    public function deleteDatabase($db_id,$user_id) {
            
            return $databasedetails = $this->MDatabaseConnections->deleteDatabase($db_id,$user_id);
        
        
    }
    
    public function getAlldatabase_new($user_id){
        
         return  $continuity = $this->Users->find()->where(['id'=>$user_id])->contain(['MDatabaseConnections','RUserDatabases'],true)->hydrate(false)->all()->toArray();
    }
    
    
    
    

    /*
      Function uniqueConnection is used for fetching the database information with
      respect to passed database connection name
      @$dbConnectionName is used to pass the database Connection Name
     */

    public function uniqueConnection($dbConnectionName = null) {


        $databasedetails = $this->MDatabaseConnections->uniqueConnection($dbConnectionName);

        return $databasedetails;
    }

}
