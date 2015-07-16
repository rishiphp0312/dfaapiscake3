<?php  
namespace App\Model\Table;
use App\Model\Entity\RAccessIndicator;
use Cake\ORM\Table;

/**
 * RAccessIndicators Model
 */
 
class RAccessIndicatorsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('r_access_indicators');
        $this->primaryKey(_RACCESSINDICATOR_ID);
        $this->addBehavior('Timestamp'); 
        $this->displayField(_RACCESSINDICATOR_INDICATOR_NAME); //used for find('list')         
    }

    /**
     * setListTypeKeyValuePairs method
     *
     * @param array $fields The fields(keys/values) for the list.
     * @return void
     */
    public function setListTypeKeyValuePairs(array $fields)
    {
        $this->primaryKey($fields[0]);
        $this->displayField($fields[1]);
    }

    /**
     * Creates record
     *
     * @param array $fieldsArray data to be created
     * @return \Cake\ORM\RulesChecker
     */
    public function createRecord($fieldsArray = [])
    {
        $RAccessIndicators = $this->newEntity();
        $RAccessIndicators = $this->patchEntity($RAccessIndicators, $fieldsArray);
        
        $result = $this->save($RAccessIndicators);
        
        if ($result) {
            return $result->{_RACCESSINDICATOR_ID};
        } else {
            return 0;
        }        
    }

    /**
     * Update record
     *
     * @param array $fieldsArray Fields to update with their Data. {DEFAULT : empty}
     * @param array $conditions The WHERE conditions for the Query. {DEFAULT : empty}
     * @return \Cake\ORM\RulesChecker
     */
    public function updateRecord($fieldsArray = [], $conditions = [])
    {
        //Initialize
        $query = $this->query();
        
        //Set
        $query->update()->set($fieldsArray)->where($conditions);
        
        //Execute
        $query->execute();
    }

    /**
     * Get Records
     *
     * @param array $conditions The WHERE conditions for the Query. {DEFAULT : empty}
     * @param array $fields The Fields to SELECT from the Query. {DEFAULT : empty}
     * @param string $type Query type {DEFAULT : empty}
     * @return void
     */
    public function getRecords(array $fields, array $conditions, $type = 'all')
    {
        $options = [];

        if(!empty($fields))
            $options['fields'] = $fields;
        if(!empty($conditions))
            $options['conditions'] = $conditions;
        
        if($type == 'list') $this->setListTypeKeyValuePairs($fields);
        
        $query = $this->find($type, $options);
        $results = $query->hydrate(false)->all();
        $data = $results->toArray();

        return $data;

    }

}