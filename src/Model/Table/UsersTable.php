<?php  
namespace App\Model\Table;
use App\Model\Entity\User;
use Cake\ORM\Table;

/**
 * User Model
 */
 
class UsersTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('m_users');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
    
        $this->belongsToMany('MDatabaseConnections', 
        [
            'targetForeignKey' => 'db_id',
            'foreignKey' => 'user_id',
             'joinTable' => 'r_user_databases',
            //  'through' => 'RUserDatabases',
        ]);
        
         
         
    }



}