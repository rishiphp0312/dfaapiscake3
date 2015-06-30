<?php

/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Event\Event;

set_time_limit(0);
ini_set('memory_limit', '2000M');

/**
 * Services Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class ServicesController extends AppController {

    //Loading Components
    public $RUserDatabasesObj;
    public $components = ['Auth', 'DevInfoInterface.CommonInterface', 'Common', 'ExcelReader'];

    public function initialize() {
        parent::initialize();
        $this->RUserDatabasesObj=TableRegistry::get('RUserDatabases');
    }

    public function beforeFilter(Event $event) {

        //parent::beforeFilter($event);
        // Allow users to register and logout.
        // You should not add the "login" action to allow list. Doing so would
        // cause problems with normal functioning of AuthComponent.

        $this->Auth->allow(['*']);
    }

    /**
     * 
     * @return JSON/boolean
     * @throws NotFoundException When the view file could not be found
     * 	or MissingViewException in debug mode.
     */
    public function serviceQuery($case = null) {
        $this->autoRender = false;
        $this->autoLayout = false; //$this->layout = '';
        $convertJson = '_YES';
        $returnData = [];
        $dbConnection = 'test';
        $dbId = '';
        $dbConnectionDetails = $this->Common->getDbDetails($dbId);

        switch ($case):

            case 'test':

                $params[] = $fields = [_INDICATOR_INDICATOR_NAME, _INDICATOR_INDICATOR_INFO];
                $params[] = $conditions = [_INDICATOR_INDICATOR_GID . ' IN' => ['POPDEN', 'AREA']];

                $returnData = $this->CommonInterface->serviceInterface('Indicator', 'getDataByParams', $params, $dbConnection);
                break;

            case 101: //Select Data using Indicator_NId -- Indicator table
                //getDataByIds($ids = null, $fields = [], $type = 'all' )
                $params[] = [317, 318, 386];
                $returnData = $this->CommonInterface->serviceInterface('Indicator', 'getDataByIds', $params, $dbConnection);
                break;

            case 102: //Select Data using Conditions -- Indicator table

                $fields = [_INDICATOR_INDICATOR_NAME, _INDICATOR_INDICATOR_INFO];
                $conditions = [_INDICATOR_INDICATOR_GID . ' IN' => ['POPDEN', 'AREA']];

                $params['fields'] = $fields;
                $params['conditions'] = $conditions;

                //getDataByParams(array $fields, array $conditions)
                $returnData = $this->CommonInterface->serviceInterface('Indicator', 'getDataByParams', $params, $dbConnection);
                break;

            case 103: //Delete Data using Indicator_NId -- Indicator table
                //deleteByIds($ids = null)
                $params[] = [383, 384, 385];
                $returnData = $this->CommonInterface->serviceInterface('Indicator', 'deleteByIds', $params, $dbConnection);
                break;

            case 104: //Delete Data using Conditions -- Indicator table

                $conditions = [_INDICATOR_INDICATOR_GID . ' IN' => ['TEST_GID', 'TEST_GID2']];

                //deleteByParams(array $conditions)
                $params['conditions'] = $conditions = [_INDICATOR_INDICATOR_GID . ' IN' => ['TEST_GID', 'TEST_GID2']];
                $returnData = $this->CommonInterface->serviceInterface('Indicator', 'deleteByParams', $params, $dbConnection);
                break;

            case 105: //Insert New Data -- Indicator table
                if ($this->request->is('post')):

                    $this->request->data = [
                        _INDICATOR_INDICATOR_NID => '384',
                        _INDICATOR_INDICATOR_NAME => 'Custom_test_name2',
                        _INDICATOR_INDICATOR_GID => 'SOME_001_TEST',
                        _INDICATOR_INDICATOR_INFO => '<?xml version="1.0" encoding="utf-8"?><metadata><Category name="Definition"><para /></Category><Category name="Method of Computation"><para /></Category><Category name="Overview"><para /></Category><Category name="Comments and Limitations"><para /></Category><Category name="Data Collection for Global Monitoring"><para /></Category><Category name="Obtaining Data:"><para /></Category><Category name="Data Availability:"><para /></Category><Category name="Treatment of Missing Values:"><para /></Category><Category name="Regional and Global Estimates:"><para /></Category><Category name="Data Availability"><para /></Category></metadata>',
                        _INDICATOR_INDICATOR_GLOBAL => '0',
                        _INDICATOR_SHORT_NAME => 'Short name',
                        _INDICATOR_KEYWORDS => 'Some Keyword',
                        _INDICATOR_INDICATOR_ORDER => '5',
                        _INDICATOR_DATA_EXIST => '1',
                        _INDICATOR_HIGHISGOOD => '1'
                    ];

                    //insertData(array $fieldsArray = $this->request->data)
                    $params['conditions'] = $conditions = $this->request->data;
                    $returnData = $this->CommonInterface->serviceInterface('Indicator', 'insertData', $params, $dbConnection);
                endif;


                break;

            case 106: //Update Data using Conditions -- Indicator table

                $fields = [
                    _INDICATOR_INDICATOR_NAME => 'Custom_test_name3',
                    _INDICATOR_INDICATOR_GID => 'SOME_003_TEST'
                ];
                $conditions = ['Indicator_NId' => '384'];

                if ($this->request->is('post')):
                    //updateDataByParams(array $fields, array $conditions)
                    $params['fields'] = $fields;
                    $params['conditions'] = $conditions;
                    $returnData = $this->CommonInterface->serviceInterface('Indicator', 'updateDataByParams', $params, $dbConnection);
                endif;

                break;

            case 107: //Bulk Insert/Update Data -- Indicator table
                //if($this->request->is('post')):
                if (true):
                    $params['filename'] = $filename = 'C:\-- Projects --\Indicator2000.xls';
                    $params['component'] = 'Indicator';
                    $params['extraParam'] = [];
                    //$returnData = $this->CommonInterface->bulkUploadXlsOrCsvForIndicator($params);                    
                    $returnData = $this->CommonInterface->serviceInterface('CommonInterface', 'bulkUploadXlsOrCsv', $params, $dbConnection);
                endif;

                break;

            case 201: //Select Data using Unit_NId -- Unit table
                //getDataByIds($ids = null, $fields = [], $type = 'all' )
                $params[] = [10, 41];
                $returnData = $this->CommonInterface->serviceInterface('Unit', 'getDataByIds', $params, $dbConnection);
                break;

            case 202: //Select Data using Conditions -- Unit table

                $params['fields'] = $fields = [_UNIT_UNIT_NAME, _UNIT_UNIT_GLOBAL];
                $params['conditions'] = $conditions = [_UNIT_UNIT_GID . ' IN' => ['POPDEN', 'AREA']];

                //getDataByParams(array $fields, array $conditions)
                $returnData = $this->CommonInterface->serviceInterface('Unit', 'getDataByParams', $params, $dbConnection);
                break;

            case 203: //Delete Data using Unit_NId -- Unit table
                //deleteByIds($ids = null)
                $params[] = [42];
                $returnData = $this->CommonInterface->serviceInterface('Unit', 'deleteByIds', $params, $dbConnection);
                break;

            case 204: //Delete Data using Conditions -- Unit table

                $params['conditions'] = $conditions = [_UNIT_UNIT_NID . ' IN' => ['SOME_001_TEST', 'SOME_003_TEST']];

                //deleteByParams(array $conditions)
                $returnData = $this->CommonInterface->serviceInterface('Unit', 'deleteByParams', $params, $dbConnection);

            case 205: //Insert New Data -- Unit table

                $this->request->data = [
                    _UNIT_UNIT_NID => '43',
                    _UNIT_UNIT_NAME => 'Custom_test_name',
                    _UNIT_UNIT_GID => 'SOME_002_TEST',
                    _UNIT_UNIT_GLOBAL => '0'
                ];

                if ($this->request->is('post')):
                    //insertData(array $fieldsArray = $this->request->data)
                    $params[] = $this->request->data;
                    $returnData = $this->CommonInterface->serviceInterface('Unit', 'insertData', $params, $dbConnection);
                endif;

                break;

            case 206: //Update Data using Conditions -- Unit table

                $fields = [
                    _UNIT_UNIT_NAME => 'Custom_test_name3',
                    _UNIT_UNIT_GID => 'SOME_003_TEST'
                ];
                $conditions = [_UNIT_UNIT_NID => '43'];

                if ($this->request->is('post')):
                    //updateDataByParams(array $fields, array $conditions)
                    $params[] = $fields;
                    $params[] = $conditions;
                    $returnData = $this->CommonInterface->serviceInterface('Unit', 'updateDataByParams', $params, $dbConnection);
                endif;

                break;

            case 207: //Bulk Insert/Update Data -- Unit table
                //if($this->request->is('post')):
                if (true):
                    $params['filename'] = $filename = 'C:\-- Projects --\Unit.xls';
                    $params['component'] = 'Unit';
                    $params['extraParam'] = [];
                    $returnData = $this->CommonInterface->serviceInterface('CommonInterface', 'bulkUploadXlsOrCsv', $params, $dbConnection);
                endif;

                break;


            // nos starting with 301 are for timeperiod


            case 301:
                // service for getting the Timeperiod details on basis of any parameter  
                // passing array $fields, array $conditions

                if (!empty($_POST['TimePeriod']) || !empty($_POST['periodicity']) || !empty($_POST['EndDate']) || !empty($_POST['StartDate']) || !empty($_POST['TimePeriod_NId'])) {

                    $conditions = array();
                    $fields = array();

                    if (isset($_POST['TimePeriod']) && !empty($_POST['TimePeriod']))
                        $conditions[_TIMEPERIOD_TIMEPERIOD] = trim($_POST['TimePeriod']);

                    if (isset($_POST['periodicity']) && !empty($_POST['periodicity']))
                        $conditions[_TIMEPERIOD_PERIODICITY] = trim($_POST['periodicity']);

                    if (isset($_POST['StartDate']) && !empty($_POST['StartDate']))
                        $conditions[_TIMEPERIOD_STARTDATE] = trim($_POST['StartDate']);

                    if (isset($_POST['EndDate']) && !empty($_POST['EndDate']))
                        $conditions[_TIMEPERIOD_ENDDATE] = trim($_POST['EndDate']);

                    if (isset($_POST['TimePeriod_NId']) && !empty($_POST['TimePeriod_NId']))
                        $conditions[_TIMEPERIOD_TIMEPERIOD_NID] = trim($_POST['TimePeriod_NId']);

                    $params[] = $conditions;

                    $params[] = $fields;

                    $getDataByTimeperiod = $this->CommonInterface->serviceInterface('Timeperiod', 'getDataByParams', $params, $dbConnection);

                    // $getDataByTimeperiod  = $this->Timeperiod->getDataByParams( $fields ,$conditions);
                    if (isset($getDataByTimeperiod) && count($getDataByTimeperiod) > 0) {
                        $returnData['data'] = $getDataByTimeperiod;
                        $returnData['success'] = true;
                    } else {
                        $returnData['success'] = false;
                        $returnData['message'] = 'No records found';
                    }
                } else {

                    $returnData['success'] = false;
                    $returnData['message'] = 'Invalid request';      //COM005; //'Invalid request'		
                }
                break;


            case 302:
                // service for deleting the time period using  any parameters   
                if (!empty($_POST['TimePeriod']) || !empty($_POST['periodicity']) || !empty($_POST['EndDate']) || !empty($_POST['StartDate']) || !empty($_POST['TimePeriod_NId'])) {

                    $conditions = array();

                    if (isset($_POST['TimePeriod']) && !empty($_POST['TimePeriod']))
                        $conditions[_TIMEPERIOD_TIMEPERIOD] = trim($_POST['TimePeriod']);

                    if (isset($_POST['periodicity']) && !empty($_POST['periodicity']))
                        $conditions[_TIMEPERIOD_PERIODICITY] = trim($_POST['periodicity']);

                    if (isset($_POST['StartDate']) && !empty($_POST['StartDate']))
                        $conditions[_TIMEPERIOD_STARTDATE] = trim($_POST['StartDate']);

                    if (isset($_POST['EndDate']) && !empty($_POST['EndDate']))
                        $conditions[_TIMEPERIOD_ENDDATE] = trim($_POST['EndDate']);

                    if (isset($_POST['TimePeriod_NId']) && !empty($_POST['TimePeriod_NId']))
                        $conditions[_TIMEPERIOD_TIMEPERIOD_NID] = trim($_POST['TimePeriod_NId']);

                    $params[] = $conditions;

                    $deleteallTimeperiod = $this->CommonInterface->serviceInterface('Timeperiod', 'deleteByParams', $params, $dbConnection);

                    //$deleteallTimeperiod  = $this->Timeperiod->deleteByParams($conditions);
                    if ($deleteallTimeperiod) {
                        $returnData['message'] = 'Record deleted successfully';
                        $returnData['success'] = true;
                        $returnData['returnvalue'] = $deleteallTimeperiod;
                    } else {
                        $returnData['success'] = false;
                    }
                } else {
                    $returnData['success'] = false;
                    $returnData['message'] = 'Invalid request';      //COM005; //'Invalid request'		
                }
                break;


            /// cases for saving Time period 
            case 303:
                // service for saving  details of timeperiod 
                $data = array();

                if (isset($_POST['TimePeriodData']) && !empty($_POST['TimePeriodData']))
                    $data[_TIMEPERIOD_TIMEPERIOD] = trim($_POST['TimePeriodData']);

                if (isset($_POST['Periodicity']) && !empty($_POST['Periodicity']))
                    $data[_TIMEPERIOD_PERIODICITY] = trim($_POST['Periodicity']);

                if (isset($_POST['TimePeriod_NId']) && !empty($_POST['TimePeriod_NId']))
                    $data[_TIMEPERIOD_TIMEPERIOD_NID] = trim($_POST['TimePeriod_NId']);

                $params[] = $data;

                $saveTimeperiodDetails = $this->CommonInterface->serviceInterface('Timeperiod', 'insertUpdateDataTimeperiod', $params, $dbConnection);

                // $saveTimeperiodDetails  = $this->Timeperiod->insertUpdateDataTimeperiod($data);
                if ($saveTimeperiodDetails) {
                    $returnData['success'] = true;
                    $returnData['message'] = 'Record inserted successfully!!';
                    $returnData['returnvalue'] = $saveTimeperiodDetails;
                } else {
                    $returnData['success'] = false;
                }

                break;

            /// cases for updating  Time period 
            case 304:
                // service for updating  details of timeperiod 
                $data = array();

                $_POST['TimePeriod_NId'] = 12;
                $_POST['Periodicity'] = 'A';

                if (isset($_POST['TimePeriodData']) && !empty($_POST['TimePeriodData']))
                    $data[_TIMEPERIOD_TIMEPERIOD] = trim($_POST['TimePeriodData']);

                if (isset($_POST['Periodicity']) && !empty($_POST['Periodicity']))
                    $data[_TIMEPERIOD_PERIODICITY] = trim($_POST['Periodicity']);

                if (isset($_POST['TimePeriod_NId']) && !empty($_POST['TimePeriod_NId']))
                    $data[_TIMEPERIOD_TIMEPERIOD_NID] = trim($_POST['TimePeriod_NId']);


                $fields = [
                    'TimePeriod' => '2029',
                ];


                $conditions = $data;

                //updateDataByParams(array $fields, array $conditions)
                $params['fields'] = $fields;
                $params['conditions'] = $conditions;

                $saveTimeperiodDetails = $this->CommonInterface->serviceInterface('Timeperiod', 'updateDataByParams', $params, $dbConnection);

                // $saveTimeperiodDetails  = $this->Timeperiod->insertUpdateDataTimeperiod($data);
                if ($saveTimeperiodDetails) {
                    $returnData['success'] = true;
                    $returnData['message'] = 'Record inserted successfully!!';
                    $returnData['returnvalue'] = $saveTimeperiodDetails;
                } else {
                    $returnData['success'] = false;
                }
                pr($returnData);
                die;
                break;


            // service no. starting with 401 are for subgroup type 
            case 401:

                if ($this->request->is('post')):
                    $data = array();
                    $data[_SUBGROUPTYPE_SUBGROUP_TYPE_GID] = $this->Common->guid();
                    $params[] = $data;

                    $saveDataforSubgroupType = $this->CommonInterface->serviceInterface('Subgroup', 'insertUpdateDataSubgroupType', $params, $dbConnection);
                    $returnData['returnvalue'] = $saveDataforSubgroupType;
                endif;

                break;

            case 402:  // service for updating  details of subgroup type 

                if ($this->request->is('post')):
                    $data = array();

                    $fields = ['Subgroup_Type_Name' => '2029'];
                    $conditions = $data;

                    $params['fields'] = $fields;
                    $params['conditions'] = $conditions;
                    $saveDataforSubgroupType = $this->CommonInterface->serviceInterface('Subgroup', 'updateDataByParamsSubgroupType', $params, $dbConnection);
                    $returnData['returnvalue'] = $saveTimeperiodDetails;
                endif;
                break;

            case 403: // service for getting the Subgroup type   details on basis of any parameter  					

                if ($this->request->is('post')):
                    $conditions = [];
                    $fields = [];
                    $params[] = $fields;
                    $params[] = $conditions;

                    $SubgroupTypeDetails = $this->CommonInterface->serviceInterface('Subgroup', 'getDataByParamsSubgroupType', $params, $dbConnection);
                    $returnData['data'] = $SubgroupTypeDetails;
                endif;
                break;

            case 404: // service for deleting the subgroup types using  any parameters
                if ($this->request->is('post')):
                    $conditions = [];
                    $params[] = $conditions;

                    $deleteallSubgroupType = $this->CommonInterface->serviceInterface('Subgroup', 'deleteByParamsSubgroupType', $params, $dbConnection);
                    $returnData['returnvalue'] = $deleteallSubgroupType;
                endif;
                break;

            // service no. starting from  501 are for subgroup
            case 501: // service for saving  subgroup  name 
                if ($this->request->is('post')):
                    $data = array();
                    $params[] = $data;
                    $saveDataforSubgroupType = $this->CommonInterface->serviceInterface('Subgroup', 'insertUpdateDataSubgroup', $params, $dbConnection);

                    $returnData['returnvalue'] = $saveDataforSubgroupType;
                endif;
                break;

            case 502: // service for updating the   subgroup  name 
                if ($this->request->is('post')):
                    $data = array();
                    $fields = [_SUBGROUP_SUBGROUP_NAME, _SUBGROUP_SUBGROUP_TYPE];

                    $params['fields'] = $fields;
                    $params['conditions'] = $data;
                    $saveDataforSubgroupType = $this->CommonInterface->serviceInterface('Subgroup', 'deleteByParamsSubgroupType', $params, $dbConnection);
                    $returnData['returnvalue'] = $saveDataforSubgroupType;
                endif;
                break;

            case 503: // service for getting the Subgroup  details on basis of any parameter  
                if ($this->request->is('post')):
                    $conditions = $fields = [];
                    $params[] = $fields;
                    $params[] = $conditions;

                    $SubgroupDetails = $this->CommonInterface->serviceInterface('Subgroup', 'getDataByParamsSubgroup', $params, $dbConnection);
                    $returnData['data'] = $SubgroupDetails;
                endif;
                break;

            case 504: // service for deleting the Subgroup Name using  any parameters
                if ($this->request->is('post')):
                    $conditions = [];
                    $params[] = $conditions;

                    $deleteallSubgroup = $this->CommonInterface->serviceInterface('Subgroup', 'deleteByParamsSubgroup', $params, $dbConnection);
                    $returnData['returnvalue'] = $deleteallSubgroup;
                endif;
                break;

            case 601: //Select Data using SubgroupVal_NId -- SubgroupVals table
                //getDataByIds($ids = null, $fields = [], $type = 'all' )
                $params[] = [317, 318, 386];
                $returnData = $this->CommonInterface->serviceInterface('SubgroupVals', 'getDataByIds', $params, $dbConnection);
                break;

            case 602: //Select Data using Conditions -- SubgroupVals table

                $fields = [_SUBGROUP_VAL_SUBGROUP_VAL, _SUBGROUP_VAL_SUBGROUP_VAL_GID];
                $conditions = [_SUBGROUP_VAL_SUBGROUP_VAL_GID . ' IN' => ['T', 'U']];

                $params['fields'] = $fields;
                $params['conditions'] = $conditions;

                //getDataByParams(array $fields, array $conditions)
                $returnData = $this->CommonInterface->serviceInterface('SubgroupVals', 'getDataByParams', $params, $dbConnection);
                break;

            case 603: //Delete Data using Indicator_NId -- SubgroupVals table
                //deleteByIds($ids = null)
                $params[] = [960, 961, 962];
                $returnData = $this->CommonInterface->serviceInterface('SubgroupVals', 'deleteByIds', $params, $dbConnection);
                break;

            case 604: //Delete Data using Conditions -- SubgroupVals table
                //deleteByParams(array $conditions)
                $params['conditions'] = $conditions = [_SUBGROUP_VAL_SUBGROUP_VAL_GID . ' IN' => ['A', 'BG']];
                $returnData = $this->CommonInterface->serviceInterface('SubgroupVals', 'deleteByParams', $params, $dbConnection);
                break;

            case 605: //Insert New Data -- SubgroupVals table
                if ($this->request->is('post')):

                    $this->request->data = [
                        _SUBGROUP_VAL_SUBGROUP_VAL_NID => '965',
                        _SUBGROUP_VAL_SUBGROUP_VAL => 'Custom_test_name2',
                        _SUBGROUP_VAL_SUBGROUP_VAL_GID => 'SOME_001_TEST',
                        _SUBGROUP_VAL_SUBGROUP_VAL_GLOBAL => '0',
                        _SUBGROUP_VAL_SUBGROUP_VAL_ORDER => '102',
                    ];

                    //insertData(array $fieldsArray = $this->request->data)
                    $params['conditions'] = $conditions = $this->request->data;
                    $returnData = $this->CommonInterface->serviceInterface('SubgroupVals', 'insertData', $params, $dbConnection);
                endif;

                break;

            case 606: //Update Data using Conditions -- SubgroupVals table

                $fields = [
                    _SUBGROUP_VAL_SUBGROUP_VAL => 'Custom_test_name3',
                    _SUBGROUP_VAL_SUBGROUP_VAL_GID => 'SOME_003_TEST'
                ];
                $conditions = [_SUBGROUP_VAL_SUBGROUP_VAL_NID => '965'];

                if ($this->request->is('post')):
                    //updateDataByParams(array $fields, array $conditions)
                    $params['fields'] = $fields;
                    $params['conditions'] = $conditions;
                    $returnData = $this->CommonInterface->serviceInterface('SubgroupVals', 'updateDataByParams', $params, $dbConnection);
                endif;

                break;

            case 607: //Bulk Insert/Update Data -- SubgroupVals table
                //if($this->request->is('post')):
                if (true):
                    $params['filename'] = $filename = 'C:\-- Projects --\Indicator2000.xls';
                    $params['component'] = 'SubgroupVals';
                    $params['extraParam'] = [];
                    //$returnData = $this->CommonInterface->bulkUploadXlsOrCsvForIndicator($params);                    
                    $returnData = $this->CommonInterface->serviceInterface('CommonInterface', 'bulkUploadXlsOrCsv', $params, $dbConnection);
                endif;

                break;

            case 701:

                //if($this->request->is('post')):
                if (true):
                    $params['filename'] = $filename = 'C:\-- Projects --\xls\Temp_Selected_ExcelFile.xls';
                    $params['component'] = 'IndicatorClassifications';
                    $params['extraParam'] = [];
                    //$returnData = $this->CommonInterface->serviceInterface('CommonInterface', 'bulkUploadXlsOrCsvForIUS', $params, $dbConnection);
                    $returnData = $this->CommonInterface->serviceInterface('CommonInterface', 'bulkUploadXlsOrCsv', $params, $dbConnection);
                endif;

                break;

            // services for Area
            case 800:

                try {

                    $returnData['success'] = true;
                    $returnData['data']['id'] = $this->Auth->user('id');
                    // echo json_encode($returnData);
                    // die('success');
                } catch (Exception $e) {
                    echo 'Exception occured while loading the project list file';
                    exit;
                }

                break;

            case 801:
                /* $_POST['Area_ID']='IND028021040';
                  $_POST['Area_Name']='dhalubaba';
                  $_POST['Area_Parent_NId']='24650';
                  $_POST['AreaShortName']='dhalubaba'	;
                 */
                //  service for getting the Area details on basis of passed parameters
                if (!empty($_POST['Area_ID']) || !empty($_POST['Area_Name']) || !empty($_POST['Area_GId']) || !empty($_POST['Area_NId']) || !empty($_POST['Area_Level']) || !empty($_POST['Data_Exist']) || !empty($_POST['AreaShortName']) || !empty($_POST['Area_Parent_NId']) || !empty($_POST['Area_Block'])) {

                    $conditions = array();

                    if (isset($_POST['Area_ID']) && !empty($_POST['Area_ID']))
                        $conditions[_AREA_AREA_ID] = trim($_POST['Area_ID']);

                    if (isset($_POST['Area_Name']) && !empty($_POST['Area_Name']))
                        $conditions[_AREA_AREA_NAME] = trim($_POST['Area_Name']);

                    if (isset($_POST['Area_GId']) && !empty($_POST['Area_GId']))
                        $conditions[_AREA_AREA_GID] = trim($_POST['Area_GId']);

                    if (isset($_POST['Area_NId']) && !empty($_POST['Area_NId']))
                        $conditions[_AREA_AREA_NID] = trim($_POST['Area_NId']);

                    if (isset($_POST['Area_Level']) && !empty($_POST['Area_Level']))
                        $conditions[_AREA_AREA_LEVEL] = trim($_POST['Area_Level']);

                    if (isset($_POST['Data_Exist']) && !empty($_POST['Data_Exist']))
                        $conditions[_AREA_DATA_EXIST] = trim($_POST['Data_Exist']);

                    if (isset($_POST['AreaShortName']) && !empty($_POST['AreaShortName']))
                        $conditions[_AREA_AREA_SHORT_NAME] = trim($_POST['AreaShortName']);

                    if (isset($_POST['Area_Parent_NId']) && !empty($_POST['Area_Parent_NId']))
                        $conditions[_AREA_PARENT_NId] = trim($_POST['Area_Parent_NId']);

                    if (isset($_POST['Area_Block']) && !empty($_POST['Area_Block']))
                        $conditions[_AREA_AREA_BLOCK] = trim($_POST['Area_Block']);

                    $params[] = $fields = [_AREA_AREA_BLOCK, _AREA_AREA_SHORT_NAME, _AREA_AREA_ID];
                    $params[] = $conditions;

                    $getAreaDetailsData = $this->CommonInterface->serviceInterface('Area', 'getDataByParams', $params, $dbConnection);
                    if ($getAreaDetailsData) {

                        $returnData['success'] = true;
                        $returnData['returnvalue'] = $getAreaDetailsData;
                    } else {
                        $returnData['success'] = false;
                    }
                } else {

                    $returnData[] = false;
                    $returnData['success'] = false;
                    $returnData['message'] = 'Invalid request';      //COM005; //'Invalid request'		
                }

                break;



            case 802:


                // service for deleting the Area using  any parameters below 
                if (!empty($_POST['Area_ID']) || !empty($_POST['Area_Name']) || !empty($_POST['Area_GId']) || !empty($_POST['Area_NId']) || !empty($_POST['Area_Level']) || !empty($_POST['Data_Exist']) || !empty($_POST['AreaShortName']) || !empty($_POST['Area_Parent_NId']) || !empty($_POST['Area_Block'])) {

                    $conditions = array();

                    if (isset($_POST['Area_ID']) && !empty($_POST['Area_ID']))
                        $conditions[_AREA_AREA_ID] = trim($_POST['Area_ID']);

                    if (isset($_POST['Area_Name']) && !empty($_POST['Area_Name']))
                        $conditions[_AREA_AREA_NAME] = trim($_POST['Area_Name']);

                    if (isset($_POST['Area_GId']) && !empty($_POST['Area_GId']))
                        $conditions[_AREA_AREA_GID] = trim($_POST['Area_GId']);

                    if (isset($_POST['Area_NId']) && !empty($_POST['Area_NId']))
                        $conditions[_AREA_AREA_NID] = trim($_POST['Area_NId']);

                    if (isset($_POST['Area_Level']) && !empty($_POST['Area_Level']))
                        $conditions[_AREA_AREA_LEVEL] = trim($_POST['Area_Level']);

                    if (isset($_POST['Data_Exist']) && !empty($_POST['Data_Exist']))
                        $conditions[_AREA_DATA_EXIST] = trim($_POST['Data_Exist']);

                    if (isset($_POST['AreaShortName']) && !empty($_POST['AreaShortName']))
                        $conditions[_AREA_AREA_SHORT_NAME] = trim($_POST['AreaShortName']);

                    if (isset($_POST['Area_Parent_NId']) && !empty($_POST['Area_Parent_NId']))
                        $conditions[_AREA_PARENT_NId] = trim($_POST['Area_Parent_NId']);


                    if (isset($_POST['Area_Block']) && !empty($_POST['Area_Block']))
                        $conditions[_AREA_AREA_BLOCK] = trim($_POST['Area_Block']);
                    $params[] = $conditions;
                    $deleteallArea = $this->CommonInterface->serviceInterface('Area', 'deleteByParams', $params, $dbConnection);
                    if ($deleteallArea) {
                        $returnData['message'] = 'Record deleted successfully';
                        $returnData['success'] = true;
                        $returnData['returnvalue'] = $deleteallArea;
                    } else {
                        $returnData['success'] = false;
                    }
                } else {
                    $returnData['success'] = false;
                    $returnData['message'] = 'Invalid request';      //COM005; //'Invalid request'		
                }
                pr($returnData);
                die;
                break;

            case 803:
                /*
                  $_POST['Area_ID']='IND028021040';
                  $_POST['Area_NId']='26503';
                  $_POST['Area_Name']='dhalubaba';
                  $_POST['Area_GId']='';
                  $_POST['Area_Parent_NId']='24650';
                  $_POST['AreaShortName']='dhalubabanew';
                 */
                // service for saving the  Area details using  any parameters below 
                if (!empty($_POST['Area_ID']) || !empty($_POST['Area_Name']) || !empty($_POST['Area_GId']) || !empty($_POST['Area_NId']) || !empty($_POST['Area_Level']) || !empty($_POST['Data_Exist']) || !empty($_POST['AreaShortName']) || !empty($_POST['Area_Parent_NId']) || !empty($_POST['Area_Block'])) {

                    $conditions = array();

                    if (isset($_POST['Area_ID']) && !empty($_POST['Area_ID']))
                        $conditions[_AREA_AREA_ID] = trim($_POST['Area_ID']);

                    if (isset($_POST['Area_Name']) && !empty($_POST['Area_Name']))
                        $conditions[_AREA_AREA_NAME] = trim($_POST['Area_Name']);

                    if (isset($_POST['Area_GId']) && !empty($_POST['Area_GId']))
                        $conditions[_AREA_AREA_GID] = trim($_POST['Area_GId']);
                    else
                        $conditions[_AREA_AREA_GID] = $this->Common->guid();

                    if (isset($_POST['Area_NId']) && !empty($_POST['Area_NId']))
                        $conditions[_AREA_AREA_NID] = trim($_POST['Area_NId']);

                    if (isset($_POST['Area_Level']) && !empty($_POST['Area_Level']))
                        $conditions[_AREA_AREA_LEVEL] = trim($_POST['Area_Level']);

                    if (isset($_POST['Data_Exist']) && !empty($_POST['Data_Exist']))
                        $conditions[_AREA_DATA_EXIST] = trim($_POST['Data_Exist']);

                    if (isset($_POST['AreaShortName']) && !empty($_POST['AreaShortName']))
                        $conditions[_AREA_AREA_SHORT_NAME] = trim($_POST['AreaShortName']);

                    if (isset($_POST['Area_Parent_NId']) && !empty($_POST['Area_Parent_NId']))
                        $conditions[_AREA_PARENT_NId] = trim($_POST['Area_Parent_NId']);
                    else
                        $conditions[_AREA_PARENT_NId] = '-1';

                    if (isset($_POST['Area_Block']) && !empty($_POST['Area_Block']))
                        $conditions[_AREA_AREA_BLOCK] = trim($_POST['Area_Block']);

                    $params[] = $conditions;
                    $insertAreadata = $this->CommonInterface->serviceInterface('Area', 'insertUpdateAreaData', $params, $dbConnection);
                    if ($insertAreadata) {
                        $returnData['message'] = 'Record saved successfully';
                        $returnData['success'] = true;
                        $returnData['returnvalue'] = $insertAreadata;
                    } else {
                        $returnData['success'] = false;
                    }
                } else {
                    $returnData['success'] = false;
                    $returnData['message'] = 'Invalid request';      //COM005; //'Invalid request'		
                }
                pr($returnData);
                die;
                break;


            case 901:

                /*
                  $_POST['Area_Level']='9';
                  $_POST['Area_Level_Name']='Area_Level_Name';
                 */
                //  service for getting the AREA LEVEL details on basis of passed parameters
                if (!empty($_POST['Level_NId']) || !empty($_POST['Area_Level']) || !empty($_POST['Area_Level_Name'])) {


                    $conditions = array();

                    if (isset($_POST['Level_NId']) && !empty($_POST['Level_NId']))
                        $conditions[_AREALEVEL_LEVEL_NID] = trim($_POST['Level_NId']);

                    if (isset($_POST['Area_Level']) && !empty($_POST['Area_Level']))
                        $conditions[_AREALEVEL_AREA_LEVEL] = trim($_POST['Area_Level']);

                    if (isset($_POST['Area_Level_Name']) && !empty($_POST['Area_Level_Name']))
                        $conditions[_AREALEVEL_LEVEL_NAME] = trim($_POST['Area_Level_Name']);

                    $params[] = $fields = [_AREALEVEL_LEVEL_NAME, _AREALEVEL_AREA_LEVEL, _AREALEVEL_LEVEL_NID];
                    $params[] = $conditions;

                    $getAreaLevelDetailsData = $this->CommonInterface->serviceInterface('Area', 'getDataByParamsAreaLevel', $params, $dbConnection);

                    if ($getAreaLevelDetailsData) {

                        $returnData['success'] = true;
                        $returnData['returnvalue'] = $getAreaLevelDetailsData;
                    } else {
                        $returnData['success'] = false;
                    }
                } else {

                    $returnData['success'] = false;
                    $returnData['message'] = 'Invalid request';      //COM005; //'Invalid request'		
                }
                pr($returnData);
                die;
                break;



            case 902:
                /*
                  $_POST['Area_Level']='93';
                  $_POST['Area_Level_Name']='Area_Level_Name3';
                 */

                // service for deleting the Area using  any parameters below 
                if (!empty($_POST['Level_NId']) || !empty($_POST['Area_Level']) || !empty($_POST['Area_Level_Name'])) {

                    $conditions = array();

                    if (isset($_POST['Level_NId']) && !empty($_POST['Level_NId']))
                        $conditions[_AREALEVEL_LEVEL_NID] = trim($_POST['Level_NId']);

                    if (isset($_POST['Area_Level']) && !empty($_POST['Area_Level']))
                        $conditions[_AREALEVEL_AREA_LEVEL] = trim($_POST['Area_Level']);

                    if (isset($_POST['Area_Level_Name']) && !empty($_POST['Area_Level_Name']))
                        $conditions[_AREALEVEL_LEVEL_NAME] = trim($_POST['Area_Level_Name']);

                    $params[] = $conditions;
                    $deleteallAreaLevel = $this->CommonInterface->serviceInterface('Area', 'deleteByParamsAreaLevel', $params, $dbConnection);
                    if ($deleteallAreaLevel) {
                        $returnData['message'] = 'Record deleted successfully';
                        $returnData['success'] = true;
                        $returnData['returnvalue'] = $deleteallAreaLevel;
                    } else {
                        $returnData['success'] = false;
                    }
                } else {
                    $returnData['success'] = false;
                    $returnData['message'] = 'Invalid request';      //COM005; //'Invalid request'		
                }
                pr($returnData);
                die;
                break;

            case 903:
                /*
                  $_POST['Area_Level']='93';
                  //$_POST['Level_NId']='11';
                  $_POST['Area_Level_Name']='Area_Level_Name3';
                 */

                // service for saving the  Area level details 
                if (!empty($_POST['Level_NId']) || !empty($_POST['Area_Level']) || !empty($_POST['Area_Level_Name'])) {

                    $conditions = array();

                    if (isset($_POST['Level_NId']) && !empty($_POST['Level_NId']))
                        $conditions[_AREALEVEL_LEVEL_NID] = trim($_POST['Level_NId']);

                    if (isset($_POST['Area_Level']) && !empty($_POST['Area_Level']))
                        $conditions[_AREALEVEL_AREA_LEVEL] = trim($_POST['Area_Level']);

                    if (isset($_POST['Area_Level_Name']) && !empty($_POST['Area_Level_Name']))
                        $conditions[_AREALEVEL_LEVEL_NAME] = trim($_POST['Area_Level_Name']);

                    $params[] = $conditions;

                    $insertAreaLeveldata = $this->CommonInterface->serviceInterface('Area', 'insertUpdateAreaLevel', $params, $dbConnection);

                    if ($insertAreaLeveldata) {
                        $returnData['message'] = 'Record saved successfully';
                        $returnData['success'] = true;
                        $returnData['returnvalue'] = $insertAreaLeveldata;
                    } else {
                        $returnData['success'] = false;
                    }
                } else {
                    $returnData['success'] = false;
                    $returnData['message'] = 'Invalid request';      //COM005; //'Invalid request'		
                }
                pr($returnData);
                die;
                break;

            case 904:
                // service for bulk upload of area excel sheet
                //if($this->request->is('post')):
                if (true):
                    $params[]['filename'] = $filename = 'C:\-- Projects --\D3A\dfa_devinfo_data_admin\webroot\data-import-formats\Area.xls';
                    //$returnData = $this->CommonInterface->bulkUploadXlsOrCsvForIndicator($params);                    
                    $returnData = $this->CommonInterface->serviceInterface('CommonInterface', 'bulkUploadXlsOrCsvForArea', $params, $dbConnection);
                    die;
                endif;

                break;

            // service for adding databases
            case 1101:
                // service for bulk upload of area excel sheet
                if ($this->request->is('post')) {

                    /*
                      $this->request->data['databaseType'] = 'mysql';
                      $this->request->data['connectionName'] = 'ashish';
                      $this->request->data['hostAddress'] = 'dgps-os';
                      $this->request->data['userName'] = 'root';
                      $this->request->data['password'] = 'root';
                      $this->request->data['port'] = '';
                      $this->request->data['databaseName'] = 'dfa_devinfo_data_admin';
                     */

                    $this->request->data['databaseType'] = strtolower($this->request->data['databaseType']);
                    $db_con = array(
                        'db_source' => $this->request->data['databaseType'],
                        'db_connection_name' => $this->request->data['connectionName'],
                        'db_host' => $this->request->data['hostAddress'],
                        'db_login' => $this->request->data['userName'],
                        'db_password' => $this->request->data['password'],
                        'db_port' => $this->request->data['port'],
                        'db_database' => $this->request->data['databaseName']
                    );

                    $data = array(
                        _DATABASE_CONNECTION_DEVINFO_DB_CONN => json_encode($db_con)
                    );
                    // $jsondata = '{"db_source":"Mysql","db_conn_name":"Testdevinfodb","db_host":"dgps-os",
                    // "db_login":"root","db_password" :"root","db_port":"","db_database":"dfa_devinfo_data_admin"}'                    ;
                    // $data = array('devinfo_db_connection' => $jsondata);
                    // $returnData = $this->Common->testConnection($data);

                    $this->request->data[_DATABASE_CONNECTION_DEVINFO_DB_CONN] = $data[_DATABASE_CONNECTION_DEVINFO_DB_CONN];
                    $this->request->data[_DATABASE_CONNECTION_DEVINFO_DB_CREATEDBY] = $this->Auth->User('id');
                    $this->request->data[_DATABASE_CONNECTION_DEVINFO_DB_MODIFIEDBY] = $this->Auth->User('id');
                    unset($this->request->data['databaseType']);
                    unset($this->request->data['connectionName']);
                    unset($this->request->data['hostAddress']);
                    unset($this->request->data['userName']);
                    unset($this->request->data['password']);
                    unset($this->request->data['port']);
                    unset($this->request->data['databaseName']);
                    $returnTestDetails = $this->Common->testConnection($data[_DATABASE_CONNECTION_DEVINFO_DB_CONN]);
                    $returnData['testconncection'] = false;
                    if ($returnTestDetails == true) {
                        $db_con_id = $this->Common->createDatabasesConnection($this->request->data);
                        $returnData['testconncection'] = true;
                        if ($db_con_id) {
                            $returnData['success'] = true;
                            $returnData['database_id'] = $db_con_id;
                        } else {
                            $returnData['success'] = false;
                        }
                    } else {
                        $returnData['success'] = false;
                    }
                }

                break;

            // service for checking unique connection name for db connection
            case 1102:
                if ($this->request->is('post')) {

                    $connectionName = $this->request->data['connectionName'];
                    $returnUniqueDetails = $this->Common->uniqueConnection($connectionName);

                    if ($returnUniqueDetails == true) {
                        $returnData['success'] = true; // new connection name 
                    } else {
                        $returnData['success'] = false; // connection already exists
                    }
                }
                
                // service for getting list of all datbase  names for db connection
            case 1103:
              
                // if ($this->request->is('post')) {

                    $user_id = $this->Auth->User('id');            
                    $returnDatabaseDetails = $this->Common->getAllDatabases($user_id);
                    if ($returnDatabaseDetails) {
                        $returnData['success'] = true; // records found
                        $returnData['data'] = $returnDatabaseDetails;                         
                    } else {
                        $returnData['success'] = false; // no  records found
                    }
               // }

                //die;
                break;
                
                  // service for deletion of specific database connection
            case 1104:
              // if ($this->request->is('post')) {

                    $db_id   = $this->request->data['dbId'];
                    $user_id = $this->Auth->User('id'); 
                    
                    $returnDatabaseDetails = $this->Common->deleteDatabase($db_id,$user_id);
                    if ($returnDatabaseDetails) {
                        $returnData['success'] = true; // records deleted
                    } else {
                        $returnData['success'] = false; // no  record deleted
                    }
               // }

                //die;
                break;   
            // service for testing db connection
            case 1105:
                   /* 
                  * 
                    * {"db_source":"Sqlserver","db_name":"Test DB","db_host":"192.168.1.11",
                        "db_login":"sa","db_password":"l9ce130","db_port":"1433","db_database":"byd_zambiainfo"}
              * 
                  
                   $this->request->data['databaseType'] = 'mysql';
                  $this->request->data['connectionName'] = 'ashish11';
                  $this->request->data['hostAddress'] = 'dgps-os';
                  $this->request->data['userName'] = 'root';
                  $this->request->data['password'] = 'root';
                  $this->request->data['port'] = '';
                  $this->request->data['databaseName'] = 'dfa_devinfo_data_admin';
                  */
                  $this->request->data['databaseType'] = 'Sqlserver';
                  $this->request->data['connectionName'] = 'ashish115599';
                  $this->request->data['hostAddress'] = '192.168.1.11';
                  $this->request->data['userName'] = 'sa99';
                  $this->request->data['password'] = 'l9ce130';
                  $this->request->data['port'] = '1433';
                  $this->request->data['databaseName'] = 'byd_zambiainfo99';
                  
                  $db_con = array(
                        'db_source' => $this->request->data['databaseType'],
                        'db_connection_name' => $this->request->data['connectionName'],
                        'db_host' => $this->request->data['hostAddress'],
                        'db_login' => $this->request->data['userName'],
                        'db_password' => $this->request->data['password'],
                        'db_port' => $this->request->data['port'],
                        'db_database' => $this->request->data['databaseName']
                    );
                $data = array(_DATABASE_CONNECTION_DEVINFO_DB_CONN => json_encode($db_con)
                                    );
                 
                $data = json_encode( $data);
                $returnTestDetails = $this->Common->testConnection($data);
                if ($returnTestDetails == true) {
                    $returnData['success'] = true;
                    $returnData['dbconnect'] = true;
                } else {
                    $returnData['success'] = false;
                    $returnData['dbconnect'] = false;
                     $returnData['error'] = json_enode($returnTestDetails);
                }
                pr($returnData);
                //die;
                break;
                
                case 1106:
                    $user_id = $this->Auth->User('id');      
                    $returnTestDetails = $this->Common->getAlldatabase_new($user_id);
                   
            pr($returnTestDetails);die;
                 break;   
        endswitch;

        return $this->service_response($returnData, $convertJson);
    }

// service query ends here 
    // - METHOD TO GET RETURN DATA
    // - METHOD TO GET RETURN DATA
    public function service_response($data, $convertJson = '_YES') {

        $data['isAuthenticated'] = false;

        if ($this->Auth->user('id')) {
            $data['isAuthenticated'] = true;
        }
        if ($convertJson == '_YES') {
            $data = json_encode($data);
        }

        if (!$this->request->is('requested')) {
            $this->response->body($data);
            return $this->response;
        } else {
            return $data;
        }
    }

}
