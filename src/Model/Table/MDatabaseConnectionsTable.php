<?php

namespace App\Model\Table;

use App\Model\Entity\MDatabaseConnection;
use Cake\ORM\Table;

/**
 * MDatabaseConnectionsTable Model
 *
 */
class MDatabaseConnectionsTable extends Table {

    /**
     * Initialize method     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        $this->table('m_database_connections');
        $this->primaryKey('ID');
        $this->addBehavior('Timestamp');
        
        $this->belongsToMany('Users', 
        [
            'targetForeignKey' => 'user_id',
            'foreignKey' => 'db_id',
            'joinTable' => 'r_user_databases',
            //   'through' => 'RUserDatabases',
        ]);
        
        
        
       
          
    }

    public function getDbNameByID($ID = null) {
        $result = false;
        $options = [];
        if (isset($ID) && !empty($ID)) {
            $options['conditions'] = array('id' => $ID, 'archived' => 0);
            //$options['fields'] => array('devinfo_db_connection') ;
        }
        if ($ID != '') {
            $MDatabaseConnections = $this->find('all', $options);
            $result = $MDatabaseConnections->hydrate(false)->first();
            $result = !empty($result) ? $result['devinfo_db_connection'] : false;
            if (!empty($result)) {
                $result = json_decode($result);
                //$result = $result->db_name;
            }
        }
        return $result;
    }

    /**
     * insertData method
     *
     * @param array $fieldsArray Fields to insert with their Data. {DEFAULT : empty}
     * @return void
     */
    public function insertData($fieldsArray = []) {
        //Create New Entity
        $databaseDetails = $this->newEntity();
        //Update New Entity Object with data
        $databaseDetails = $this->patchEntity($databaseDetails, $fieldsArray);
        //Create new row and Save the Data
        if ($this->save($databaseDetails)) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * uniqueConnection method
     *
     * @param  $connectionName the connection name uniqueness  {DEFAULT : empty}
     * @return void
     */
    public function uniqueConnection($connectionName = null) {

        $options = array();
        $getconnectionname = array();
        $options['fields'] = array(_DATABASE_CONNECTION_DEVINFO_DB_CONN);
        //$options['devinfo_db_connection']=
        $MDatabaseConnections = $this->find('all', $options);
        $result = $MDatabaseConnections->hydrate(false)->all();
        if (isset($result) && !empty($result)) {
            foreach ($result as $index => $valuedb) {
                //pr($valuedb);
                $connectionObject = json_decode($valuedb[_DATABASE_CONNECTION_DEVINFO_DB_CONN], true);
                //  pr($connectionObject);
                //echo $connectionName;die;
                if (isset($connectionObject['db_connection_name'])) {
                    if (strtolower($connectionName) == strtolower($connectionObject['db_connection_name'])) {
                        return false; // connection already exists
                    }
                }
                // new connection 
            } // end of foreach
        } // end of if
        return true;
    }

    /**
     * uniqueConnection method
     *
     * @param  $connectionName the connection name uniqueness  {DEFAULT : empty}
     * @return void
     */
    public function getAllDatabases($user_id = null) {

        $options = array();
        $data = array();
        $getconnectionname = array();
        if (!empty($user_id))
            $options['conditions'] = array(_DATABASE_CONNECTION_DEVINFO_DB_CREATEDBY => $user_id,_DATABASE_CONNECTION_DEVINFO_DB_ARCHIVED=>0);
       
        $options['fields'] = array(_DATABASE_CONNECTION_DEVINFO_DB_CONN, _DATABASE_CONNECTION_DEVINFO_DB_ID);
        //$options['devinfo_db_connection']=
        $MDatabaseConnections = $this->find('all', $options);
        $result = $MDatabaseConnections->hydrate(false)->all();
        if (isset($result) && !empty($result)) {
            foreach ($result as $index => $valuedb) {
                $connectionObject = json_decode($valuedb[_DATABASE_CONNECTION_DEVINFO_DB_CONN], true);
                if (isset($connectionObject['db_database']) && !empty($connectionObject['db_database'])) {
                    $data[$index]['id'] = $valuedb[_DATABASE_CONNECTION_DEVINFO_DB_ID];
                    $data[$index]['dbName'] = $connectionObject['db_database'];
                }
            }
        }

        return $data;
    }

    /**
     * deleteDatabase method
     * @param  $user_id the database id   {DEFAULT : empty}
     * @param  $db_id the database id   {DEFAULT : empty}
     * @return void
     */
    public function deleteDatabase($db_id = null, $user_id = null) {

        $fieldsArray = array();


        $fieldsArray[_DATABASE_CONNECTION_DEVINFO_DB_ARCHIVED] = '1'; // means deleted 

        if (!empty($db_id))
            $fieldsArray[_DATABASE_CONNECTION_DEVINFO_DB_ID] = $db_id;

        if (!empty($user_id))
            $fieldsArray[_DATABASE_CONNECTION_DEVINFO_DB_MODIFIEDBY] = $user_id;
            

        if (!empty($db_id) && !empty($user_id)) {
            
            $databaseDetails = $this->newEntity();
            //Update New Entity Object with data
            $databaseDetails = $this->patchEntity($databaseDetails, $fieldsArray);
            //Create new row and Save the Data
            if ($this->save($databaseDetails)) {
                return 1;
            } else {
                return 0;
            }
        }
    }

}
