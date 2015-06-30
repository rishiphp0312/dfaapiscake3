<?php  
namespace App\Model\Table;
use App\Model\Entity\MRole;
use Cake\ORM\Table;

/**
 * MRoles Model
 */
 
class MRolesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('m_roles');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');     
        
         $this->belongsToMany('RUserDatabases',[
            'targetForeignKey' => 'user_database_id',
            'foreignKey' => 'role_id',
            'joinTable' => 'r_user_database_roles',
            //   'through' => 'RUserDatabases',
        ]);
        
         
    }
    
    



}