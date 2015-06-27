<?php  
namespace App\Model\Table;

use App\Model\Entity\MDatabaseConnections;
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
    public function initialize(array $config)
    {
        $this->table('m_database_connections');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
    }
	
	

	public function getDbNameByID($ID=null)
	{	
		$result = false;
		
		$options =[];
		
		if(isset($ID) && !empty($ID)) {
		    $options['conditions'] = array('id'=>$ID,'archived' => 0);
		    //$options['fields'] => array('devinfo_db_connection') ;
		}	
		
		
		if ($ID != '') {
			
	  
			$MDatabaseConnections = $this->find('all',$options);
		    $result = $MDatabaseConnections->hydrate(false)->first();   
		
			$result = !empty($result) ? $result['devinfo_db_connection'] : false;
			
			if (!empty($result)) {
				$result = json_decode($result);				
				//$result = $result->db_name;
			}
		}
		
		
		
		return $result;
	}
}
