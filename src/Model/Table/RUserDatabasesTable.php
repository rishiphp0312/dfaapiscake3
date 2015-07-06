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
    
    
    /*
     * Function to add new user association with the database 
     */
    public function addUserDatabases($fieldsArray = []) {
        //Create New Entity
        $databaseDetails = $this->newEntity();
        //Update New Entity Object with data
        $databaseDetails = $this->patchEntity($databaseDetails, $fieldsArray);
        //Create new row and Save the Data
        if ($this->save($databaseDetails)) {
            return $databaseDetails->id;
        } else {
            return 0;
        }
    }


    
    /**
     * deleteDatabase method
   
     * @return void
     */
    public function deleteUserDatabase($ids = []) {

        $result = $this->deleteAll([_RUSERDB_ID . ' IN' => $ids]); //_RUSERDBROLE_ID

        return $result;
    }
    
    
    
    /**
     * find ids  for specific users  method
     * @param  $user_id the  user_id with respect to the rows of users in  r_user_databases 
    
     * @return void
     */
    
    public function findUserDatabases($userId = [],$dbId=null) {
        $returnIds=[];
        if (!empty($fields))
        $options['fields'] = array(_RUSERDB_ID);
        $options['conditions'] = [_RUSERDB_USER_ID . ' IN' => $userId,_RUSERDB_DB_ID=>$dbId];
        $data = $this->find('all',$options)->hydrate(false)->all()->toArray();         
        if(isset($data)){
            foreach($data as $index => $valueId){               
             $returnIds[]=$valueId[_RUSERDBROLE_ID];
            }
        }    

        return $returnIds;
    }
	
	/*
	
	check user is already added to db or not 
	
	*/
	
	public function checkUserDbRelation($userId,$dbId){
        
		$count = $this->find()->where(['user_id'=>$userId,'db_id'=>$dbId])->hydrate(false)->count();
         //pr($count);
        return $count;
    }
	
	

}
