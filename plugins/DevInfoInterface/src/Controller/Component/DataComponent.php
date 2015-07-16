<?php

namespace DevInfoInterface\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;

/**
 * Data Component
 */
class DataComponent extends Component {

    // The other component your component uses
    public $components = ['Auth', 'DevInfoInterface.IndicatorClassifications', 'DevInfoInterface.IcIus', 'DevInfoInterface.Timeperiod', 'DevInfoInterface.IndicatorUnitSubgroup'];
    public $AreaObj = NULL;
    public $AreaLevelObj = NULL;
    public $DataObj = NULL;
    public $TimeperiodObj = NULL;
    public $footnoteObj = NULL;
    public $Indicator = NULL;
    public $IndicatorUnitSubgroupObj = NULL;
    public $SourceObj = NULL;
    public $IcIusObj = NULL;
    public $delm1 = '';
    public $delm2 = _DELEM2;

    public function initialize(array $config) {
        parent::initialize($config);
        $this->AreaObj = TableRegistry::get('DevInfoInterface.Areas');
        $this->DataObj = TableRegistry::get('DevInfoInterface.Data');
        $this->TimeperiodObj = TableRegistry::get('DevInfoInterface.TimePeriods');
        $this->AreaLevelObj = TableRegistry::get('DevInfoInterface.AreaLevel');
        $this->Indicator = TableRegistry::get('DevInfoInterface.Indicator');
        $this->IndicatorUnitSubgroupObj = TableRegistry::get('DevInfoInterface.IndicatorUnitSubgroup');
        $this->FootnoteObj = TableRegistry::get('DevInfoInterface.Footnote');
        $this->IndicatorClassificationsObj = TableRegistry::get('DevInfoInterface.IndicatorClassifications');
        $this->IcIusObj = TableRegistry::get('DevInfoInterface.IcIus');
    }

    public function getIusDataCollection($iusArray) {


        $tempDataAr = array(); // temproryly store data for all element name		

        foreach ($iusArray as $ius) {
            //pr($ius);
            $iusAr = explode($this->delm2, $ius);

            $iGid = $iusAr[0];
            $uGid = $iusAr[1];

            if (count($iusAr) == '3') {
                $sGid = $iusAr[2];
            } else {
                $sGid = '';
            }

            //
            $data = $this->IndicatorUnitSubgroup->getIusNidsDetails($iGid, $uGid, $sGid);

            //	pr($data);die;

            foreach ($data as $valueIus) {

                $iu = $valueIus['indicator'][_IUS_INDICATOR_NID] . '_' . $valueIus['unit'][_UNIT_UNIT_NID];
                $tempDataAr['ind'][$valueIus['indicator'][_INDICATOR_INDICATOR_NID]][0] = $iGid;
                $tempDataAr['ind'][$valueIus['indicator'][_INDICATOR_INDICATOR_NID]][1] = $valueIus['indicator'][_INDICATOR_INDICATOR_NAME];

                $tempDataAr['unit'][$valueIus['unit'][_UNIT_UNIT_NID]][0] = $uGid;
                $tempDataAr['unit'][$valueIus['unit'][_UNIT_UNIT_NID]][1] = $valueIus['unit'][_UNIT_UNIT_NAME];

                $tempDataAr['sg']['ius'][$valueIus[_IUS_IUSNID]][0] = $valueIus['subgroup_val'][_SUBGROUP_VAL_SUBGROUP_VAL_GID];
                $tempDataAr['sg']['ius'][$valueIus[_IUS_IUSNID]][1] = $valueIus['subgroup_val'][_SUBGROUP_VAL_SUBGROUP_VAL];

                $tempDataAr['ind']['ius'][$valueIus[_IUS_IUSNID]][0] = $valueIus['indicator'][_INDICATOR_INDICATOR_GID];
                $tempDataAr['ind']['ius'][$valueIus[_IUS_IUSNID]][1] = $valueIus['indicator'][_INDICATOR_INDICATOR_NAME];

                $tempDataAr['unit']['ius'][$valueIus[_IUS_IUSNID]][0] = $valueIus['unit'][_UNIT_UNIT_GID];
                $tempDataAr['unit']['ius'][$valueIus[_IUS_IUSNID]][1] = $valueIus['unit'][_UNIT_UNIT_NAME];

                $tempDataAr['IUNid']['ius'][$valueIus[_IUS_IUSNID]] = 'IU_' . $iu;
                if ($sGid != '') {

                    $tempDataAr['sg'][$valueIus['subgroup_val'][_SUBGROUP_VAL_SUBGROUP_VAL_NID]][0] = $sGid;
                    $tempDataAr['sg'][$valueIus['subgroup_val'][_SUBGROUP_VAL_SUBGROUP_VAL_NID]][1] = $valueIus['subgroup_val'][_SUBGROUP_VAL_SUBGROUP_VAL];
                } else {

                    $tempDataAr['sg'][$valueIus['subgroup_val'][_SUBGROUP_VAL_SUBGROUP_VAL_NID]][0] = $valueIus['subgroup_val'][_SUBGROUP_VAL_SUBGROUP_VAL_GID];
                    $tempDataAr['sg'][$valueIus['subgroup_val'][_SUBGROUP_VAL_SUBGROUP_VAL_NID]][1] = $valueIus['subgroup_val'][_SUBGROUP_VAL_SUBGROUP_VAL];
                }

                $tempDataAr['iusnids'][] = $valueIus[_IUS_IUSNID];
            }
        }
        return $tempDataAr;
    }

    /**
    * getDEsearchData to get the details of search on basis of IUSNid,
    * @parameters passed in conditions will be areanid , TimeperiodNid ,IUSNid
    * $iusgids will be passed as array in extra 
    * returns data value with source
    * @access public
    */
    
    public function getDEsearchData($fields = [], $conditions = [], $extra = []) {

        $iusnidData = [];

        $iusNids = $this->getIusDataCollection($extra);
        $returnediusNids = $iusNids['iusnids']; //iusnids 
        //$returnediusNids= [2398,2660,23930];		
        // getting all classifications 
        $fields1 = [_IC_IC_NAME, _IC_IC_GID, _IC_IC_NID, _IC_IC_TYPE];
        $conditions1 = [];
        $sourceList = $this->IndicatorClassifications->getDataByParams($fields1, $conditions1, 'all');

        // structuring classification for name and gid
        $classificationArray = array();
        foreach ($sourceList as $index => $value) {
            $classificationArray[$value[_IC_IC_NID]]['IC_GId'] = $value[_IC_IC_GID];
            $classificationArray[$value[_IC_IC_NID]]['IC_Name'] = $value[_IC_IC_NAME];
            $classificationArray[$value[_IC_IC_NID]]['IC_Type'] = $value[_IC_IC_TYPE];
        }


        // getting all timperiod list 
        $fields2 = [_TIMEPERIOD_TIMEPERIOD_NID, _TIMEPERIOD_TIMEPERIOD];
        $conditions2 = [];
        $timeperiodList = $this->Timeperiod->getDataByParams($fields2, $conditions2, 'list');

        // getting all footnote list  
        $footnoteList = $this->FootnoteObj->find('all')->combine(_FOOTNOTE_NId, _FOOTNOTE_VAL)->toArray();

        $conditions[_MDATA_IUSNID . ' IN '] = $returnediusNids;
        $fields = [];
        $data = $this->DataObj->getDataByParams($fields, $conditions, 'all');

        $alldataIusnids = []; // store all iusnids from data table

        $iusnidData = [];

        foreach ($data as $index => $value) {

            $IUNId = 'IU_' . $value['IUNId'];
            $iusnid = $value[_MDATA_IUSNID];

            $iusnidData[$IUNId][$iusnid]['dNid'] = $value[_MDATA_NID];
            $iusnidData[$IUNId][$iusnid]['tp'] = $value[_MDATA_TIMEPERIODNID];
            $iusnidData[$IUNId][$iusnid]['dv'] = $value[_MDATA_DATAVALUE];
            $iusnidData[$IUNId][$iusnid]['src'] = $value[_MDATA_SOURCENID];
            $iusnidData[$IUNId][$iusnid]['sGid'] = $iusNids['sg'][$value[_MDATA_SUBGRPNID]][0]; //sbgrp gid 
            $iusnidData[$IUNId][$iusnid]['sName'] = $iusNids['sg'][$value[_MDATA_SUBGRPNID]][1]; //sbgrp  name 
            $iusnidData[$IUNId][$iusNids]['footnote'] = (!empty($value[_MDATA_FOOTNOTENID])) ? $footnoteList[$value[_MDATA_FOOTNOTENID]] : '';
            $iusnidData[$IUNId][$iusnid]['iusnid'] = $value[_MDATA_IUSNID];
            $alldataIusnids[] = $value[_MDATA_IUSNID];
            $alldataIndicators[$value[_MDATA_IUSNID]] = $value[_MDATA_INDICATORNID]; // storing ind index w.r.t iusnids
            $alldataUnits[$value[_MDATA_IUSNID]] = $value[_MDATA_UNITNID]; // storing unit index w.r.t iusnids
        }


        $finalArray = [];

        // pr($returnediusNids);
        //pr($iusNids['IUNid']['ius']);die;
        foreach ($returnediusNids as $index => $iusnidvalue) {
            // first classification         
            if (in_array($iusnidvalue, $alldataIusnids) == true) {
                $prepareIU = 'IU_' . $alldataIndicators[$iusnidvalue] . '_' . $alldataUnits[$iusnidvalue]; // using IU index for array 
            } else {

                $prepareIU = $iusNids['IUNid']['ius'][$iusnidvalue]; //get from array 
            }
            //$prepareIU = 'IU_' .$iusnidData['IUNid'];
            //$finalArray[$icnid]['icName'] = $classificationArray[$icnid]['IC_Name'];
            // $finalArray[$icnid]['iGid'] = $classificationArray[$icnid]['IC_GId'];
            $finalArray['iu'][$prepareIU]['iname'] = $iusNids['ind']['ius'][$iusnidvalue][1]; //name 
            $finalArray['iu'][$prepareIU]['iGid'] = $iusNids['ind']['ius'][$iusnidvalue][0];
            $finalArray['iu'][$prepareIU]['uName'] = $iusNids['unit']['ius'][$iusnidvalue][1];
            $finalArray['iu'][$prepareIU]['uGid'] = $iusNids['unit']['ius'][$iusnidvalue][0];
            //pr($iusNids);die('hua');



            if (in_array($iusnidvalue, $alldataIusnids) == true) {
                $finalArray['iu'][$prepareIU]['subgrps'][$iusnidvalue] = $iusnidData[$prepareIU][$iusnidvalue];
            } else {
                $finalArray['iu'][$prepareIU]['subgrps'][$iusnidvalue] = ['dNid' => '', 'sName' => $iusNids['sg']['ius'][$iusnidvalue][1], 'sGid' => $iusNids['sg']['ius'][$iusnidvalue][0],
                    'iusnid' => $iusnidvalue, 'dv' => '', 'tp' => '', 'src' => '', 'footnote' => ''];
            }
        }

        return $finalArray;
    }

//  function ends here 
}
