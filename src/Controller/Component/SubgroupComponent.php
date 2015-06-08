<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;

/**
 * Subgroup Component
 */
class SubgroupComponent extends Component
{

    /**
     * getDataByIds method
     *
     * @param array $conditions Conditions on which to search. {DEFAULT : empty}
     * @param array $fields Fields to fetch. {DEFAULT : empty}
     * @return void
     */
    public function getDataByIds($ids = null, $fields = [], $type = 'all' )
    {
        
        $UTIndicatorEn = TableRegistry::get('UTIndicatorEn');

        return $UTIndicatorEn->getDataByIds($ids, $fields, $type);

    }


    /**
     * getDataByParams method
     *
     * @param array $conditions Conditions on which to search. {DEFAULT : empty}
     * @param array $fields Fields to fetch. {DEFAULT : empty}
     * @return void
     */
    public function getDataByParams(array $conditions, array $fields)
    {
        
        App::import('Model', 'UTIndicatorEn');
        $UTIndicatorEn = new UTIndicatorEn();

        return $UTIndicatorEn->getDataByParams($conditions, $fields);

    }
	
	 /**
     * savesingleSubgroupTypeName method is used to add new subgroup type      *
     * @param Subgroup_Type_Name is used for subgroup type name. {DEFAULT : empty}
     * @param Subgroup_Type_Order is used for subgroup type order {DEFAULT : 1}
	 * @param Subgroup_Type_Global {DEFAULT : 0}
     * @return void
     */
    public function savesingleSubgroupTypeName($Subgroup_Type_Name,$Subgroup_Type_Order,$Subgroup_Type_Global)
    {
        
        $UTSubgroupTypeEnTable = TableRegistry::get('UTSubgroupTypeEn');

        return $UTSubgroupTypeEnTable->savesingleSubgroupTypeName($Subgroup_Type_Name,$Subgroup_Type_Order,$Subgroup_Type_Global);

    }


}
