<?php
namespace App\Model\Table;

use App\Model\Entity\RUserDatabase;
use Cake\ORM\Table;

/**
 * RUserDatabasesTable Model
 *
 */
class  RUserDatabasesTable extends Table {

    /**
     * Initialize method     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        $this->table('r_user_databases');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        
        /* 
         * $this->belongsTo('MDatabaseConnections',[
            //'targetForeignKey' => 'db_id',
            'foreignKey' => 'db_id']);
         $this->belongsTo('Users',[
            //'targetForeignKey' => 'db_id',
            'foreignKey' => 'user_id']);
         * 
         */
         $this->belongsToMany('MRoles', 
        [
            'targetForeignKey' => 'role_id',
            'foreignKey' => 'user_database_id',
            'joinTable' => 'r_user_database_roles',
        ]);
         
         
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
     * deleteDatabase method
     * @param  $user_id the database id   {DEFAULT : empty}
     * @param  $db_id the database id   {DEFAULT : empty}
     * @return void
     */
    public function deleteDatabase($db_id = null, $user_id = null) {

        
    }

}
