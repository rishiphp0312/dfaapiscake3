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



}