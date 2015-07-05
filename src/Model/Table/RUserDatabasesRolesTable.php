<?php  
namespace App\Model\Table;
use App\Model\Entity\RUserDatabasesRole;
use Cake\ORM\Table;

/**
 * MRoles Model
 */
 
class RUserDatabasesRolesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('r_user_database_roles');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
    }
    
    
    /*
     * Function to add new user roles with the database 
     */
    public function addUserRoles($fieldsArray = []) {
        //Create New Entity
        $databaseUserRoles = $this->newEntity();
        //Update New Entity Object with data
        $databaseUserRoles = $this->patchEntity($databaseUserRoles, $fieldsArray);
        //Create new row and Save the Data
        if ($this->save($databaseUserRoles)) {
            return $databaseUserRoles->id;
        } else {
            return 0;
        }
    }
    
    
     
    /**
     * delete ROLES  method
     @$RUD_ids is the array of RUD 
     * @return void
     */
    public function deleteUserRolesDatabase($RUD_ids = []) {

        $result = $this->deleteAll([_RUSERDBROLE_USER_DB_ID . ' IN' => $RUD_ids]); 

        return $result;
    }
	
	 /**
     * deleteUserRoles method when modifying user their roles may be updated 
       @$RUD_ids is the array of RUD 
     * @return void
     */
	public function deleteUserRoles($RUD_ids=[],$roledids=[],$type=null) {

	    if($type=='E')
		$result = $this->deleteAll([_RUSERDBROLE_USER_DB_ID . ' IN' => $RUD_ids,_RUSERDBROLE_ROLE_ID. ' IN' => $roledids]);
	    else
        $result = $this->deleteAll([_RUSERDBROLE_USER_DB_ID . ' IN' => $RUD_ids,_RUSERDBROLE_ROLE_ID. ' NOT IN' => $roledids]); 

        return $result;
    }
	

    /**
     * find ids  for specific users  method
     * @param  $ids the  ids in array for rows will be deleted 
    
     * @return void
     */
    
    public function findRoleIDDatabase($RUD_ids = []) {
         $returnRoleIds =array();
        if (!empty($fields))
          $options['fields'] = array(_RUSERDBROLE_ROLE_ID);
       
        $options['conditions'] = array(_RUSERDBROLE_USER_DB_ID. ' IN' => $RUD_ids);
        $query = $this->find('all',$options); //
        $results = $query->hydrate(false)->all();

        $dataRoleIds = $results->toArray();
        if(isset($dataRoleIds)){
            foreach( $dataRoleIds as $index=>$valueId){               
             $returnRoleIds[]=$valueId[_RUSERDBROLE_ROLE_ID];
            }
        }    
        return $returnRoleIds;
     
    }

    
    
     /**
     * find ids  for specific findUserRolesDatabases  method
     * @param  $user_id the  user_id with respect to the rows of users in  r_user_databases 
    
     * @return void
     */
    


}