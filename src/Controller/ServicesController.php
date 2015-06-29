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
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Event\Event;


/**
 * Services Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class ServicesController extends AppController
{
    //Loading Components
 
	public $components = ['DevInfoInterface.CommonInterface','Common','ExcelReader'];

    public function initialize()
    {
        parent::initialize();
    }
	
	public function beforeFilter(Event $event) 
	{
		
		//parent::beforeFilter($event);
		// Allow users to register and logout.
		// You should not add the "login" action to allow list. Doing so would
		// cause problems with normal functioning of AuthComponent.
	
		//$this->Auth->allow(['serviceQuery']);
	}

    /**
	* 
	* @return JSON/boolean
	* @throws NotFoundException When the view file could not be found
	*	or MissingViewException in debug mode.
	*/
    public function serviceQuery($case = null)
    {
      
	  $this->autoRender = false;
        $this->autoLayout = false;//$this->layout = '';
		$convertJson = '_YES';
		$returnData = [];
        $dbConnection = 'test';
		$dbId = '';
        $dbConnectionDetails = $this->getDbDetails($dbId);

        switch($case):

            case 'test':
                
                $params[] = $fields = [_INDICATOR_INDICATOR_NAME, _INDICATOR_INDICATOR_INFO];
                $params[] = $conditions = [_INDICATOR_INDICATOR_GID.' IN'=>['POPDEN', 'AREA']];

                $returnData = $this->CommonInterface->serviceInterface('Indicator', 'getDataByParams', $params, $dbConnection);
                break;

            case 101: //Select Data using Indicator_NId -- Indicator table

                //getDataByIds($ids = null, $fields = [], $type = 'all' )
                $params[] = [317,318,386];
                $returnData = $this->CommonInterface->serviceInterface('Indicator', 'getDataByIds', $params, $dbConnection);
                break;

            case 102: //Select Data using Conditions -- Indicator table
                
                $fields = [_INDICATOR_INDICATOR_NAME, _INDICATOR_INDICATOR_INFO];
                $conditions = [_INDICATOR_INDICATOR_GID.' IN'=>['POPDEN', 'AREA']];
                
                $params['fields'] = $fields;
                $params['conditions'] = $conditions;

                //getDataByParams(array $fields, array $conditions)
                $returnData = $this->CommonInterface->serviceInterface('Indicator', 'getDataByParams', $params, $dbConnection);
                break;

            case 103: //Delete Data using Indicator_NId -- Indicator table
                
                //deleteByIds($ids = null)
                $params[] = [383,384,385];
                $returnData = $this->CommonInterface->serviceInterface('Indicator', 'deleteByIds', $params, $dbConnection);
                break;

            case 104: //Delete Data using Conditions -- Indicator table
                
                $conditions = [_INDICATOR_INDICATOR_GID.' IN'=>['TEST_GID', 'TEST_GID2']];

                //deleteByParams(array $conditions)
                $params['conditions'] = $conditions = [_INDICATOR_INDICATOR_GID.' IN'=>['TEST_GID', 'TEST_GID2']];
                $returnData = $this->CommonInterface->serviceInterface('Indicator', 'deleteByParams', $params, $dbConnection);
                break;

            case 105: //Insert New Data -- Indicator table
                if($this->request->is('post')):

                    $this->request->data = [
                                    'Indicator_NId'=>'384',
                                    'Indicator_Name'=>'Custom_test_name2',
                                    'Indicator_GId'=>'SOME_001_TEST',
                                    'Indicator_Info'=>'<?xml version="1.0" encoding="utf-8"?><metadata><Category name="Definition"><para /></Category><Category name="Method of Computation"><para /></Category><Category name="Overview"><para /></Category><Category name="Comments and Limitations"><para /></Category><Category name="Data Collection for Global Monitoring"><para /></Category><Category name="Obtaining Data:"><para /></Category><Category name="Data Availability:"><para /></Category><Category name="Treatment of Missing Values:"><para /></Category><Category name="Regional and Global Estimates:"><para /></Category><Category name="Data Availability"><para /></Category></metadata>',
                                    'Indicator_Global'=>'0',
                                    'Short_Name'=>'Short name',
                                    'Keywords'=>'Some Keyword',
                                    'Indicator_Order'=>'5',
                                    'Data_Exist'=>'1',
                                    'HighIsGood'=>'1'
                                    ];
                
                    //insertData(array $fieldsArray = $this->request->data)
                    $params['conditions'] = $conditions = $this->request->data;
                    $returnData = $this->CommonInterface->serviceInterface('Indicator', 'insertData', $params, $dbConnection);
                endif;
				

                break;

            case 106: //Update Data using Conditions -- Indicator table
                
                $fields = [
                          'Indicator_Name'=>'Custom_test_name3',
                          'Indicator_GId'=>'SOME_003_TEST'
                          ];
                $conditions = ['Indicator_NId'=>'384'];

                if($this->request->is('post')):
                    //updateDataByParams(array $fields, array $conditions)
                    $params['fields'] = $fields;
                    $params['conditions'] = $conditions;
                    $returnData = $this->CommonInterface->serviceInterface('Indicator', 'updateDataByParams', $params, $dbConnection);
                endif;

                break;
                
            case 107: //Bulk Insert/Update Data -- Indicator table
                
                //if($this->request->is('post')):
                if(true):
                    $params[]['filename'] = $filename = 'C:\-- Projects --\Indicator2000.xls';
                    //$returnData = $this->CommonInterface->bulkUploadXlsOrCsvForIndicator($params);                    
                    $returnData = $this->CommonInterface->serviceInterface('CommonInterface', 'bulkUploadXlsOrCsvForIndicator', $params, $dbConnection);                    
                endif;
                
                break;

            case 201: //Select Data using Unit_NId -- Unit table

                //getDataByIds($ids = null, $fields = [], $type = 'all' )
                $params[] = [10,41];
                $returnData = $this->CommonInterface->serviceInterface('Unit', 'getDataByIds', $params, $dbConnection);
                break;

            case 202: //Select Data using Conditions -- Unit table
                
                $params['fields'] = $fields = ['Unit_Name', 'Unit_Global'];
                $params['conditions'] = $conditions = ['Unit_GId IN'=>['POPDEN', 'AREA']];

                //getDataByParams(array $fields, array $conditions)
                $returnData = $this->CommonInterface->serviceInterface('Unit', 'getDataByParams', $params, $dbConnection);
                break;

            case 203: //Delete Data using Unit_NId -- Unit table
                
                //deleteByIds($ids = null)
                $params[] = [42];
                $returnData = $this->CommonInterface->serviceInterface('Unit', 'deleteByIds', $params, $dbConnection);
                break;

            case 204: //Delete Data using Conditions -- Unit table
                
                $params['conditions'] = $conditions = ['Unit_GId IN'=>['SOME_001_TEST', 'SOME_003_TEST']];

                //deleteByParams(array $conditions)
                $returnData = $this->CommonInterface->serviceInterface('Unit', 'deleteByParams', $params, $dbConnection);

            case 205: //Insert New Data -- Unit table
                
                $this->request->data = [
                                    'Unit_NId'=>'43',
                                    'Unit_Name'=>'Custom_test_name',
                                    'Unit_GId'=>'SOME_002_TEST',
                                    'Unit_Global'=>'0'
                                    ];

                if($this->request->is('post')):
                    //insertData(array $fieldsArray = $this->request->data)
                    $params[] = $this->request->data;
                    $returnData = $this->CommonInterface->serviceInterface('Unit', 'insertData', $params, $dbConnection);
                endif;

                break;

            case 206: //Update Data using Conditions -- Unit table
                
                $fields = [
                          'Unit_Name'=>'Custom_test_name3',
                          'Unit_GId'=>'SOME_003_TEST'
                          ];
                $conditions = ['Unit_NId'=>'43'];

                if($this->request->is('post')):
                    //updateDataByParams(array $fields, array $conditions)
                    $params[] = $fields;
                    $params[] = $conditions;
                    $returnData = $this->CommonInterface->serviceInterface('Unit', 'updateDataByParams', $params, $dbConnection);
                endif;

            break;

            case 207: //Bulk Insert/Update Data -- Unit table
                
                if($this->request->is('post')):
                    $params['filename'] = $filename = 'C:\-- Projects --\Bulk_unit_test_file.xlsx';
                    $returnData = $this->CommonInterface->serviceInterface('CommonInterface', 'bulkUploadXlsOrCsvForUnit', $params, $dbConnection);
                endif;

            break;

           
			// nos starting with 301 are for timeperiod
			
			
			case 301:
				// service for getting the Timeperiod details on basis of any parameter  
				// passing array $fields, array $conditions
			$_POST['TimePeriod']='2060';
			$_POST['periodicity']='C';
			if(!empty($_POST['TimePeriod']) || !empty($_POST['periodicity']) || !empty($_POST['EndDate']) || !empty($_POST['StartDate']) || !empty($_POST['TimePeriod_NId'])){
				
				$conditions = array();
			    $fields = array();	

				if(isset($_POST['TimePeriod']) && !empty($_POST['TimePeriod']))
                $conditions[_TIMEPERIOD_TIMEPERIOD] = trim($_POST['TimePeriod']);	
					
    			if(isset($_POST['periodicity']) && !empty($_POST['periodicity']))
				$conditions[_TIMEPERIOD_PERIODICITY] = trim($_POST['periodicity']);	
			
				if(isset($_POST['StartDate']) && !empty($_POST['StartDate']))
                $conditions[_TIMEPERIOD_STARTDATE] = trim($_POST['StartDate']);	
			
			    if(isset($_POST['EndDate']) && !empty($_POST['EndDate']))
                $conditions[_TIMEPERIOD_ENDDATE] = trim($_POST['EndDate']);	
			
			    if(isset($_POST['TimePeriod_NId']) && !empty($_POST['TimePeriod_NId']))
                $conditions[_TIMEPERIOD_TIMEPERIOD_NID] = trim($_POST['TimePeriod_NId']);
			
			    $params[] = $fields = [_TIMEPERIOD_TIMEPERIOD_NID, _TIMEPERIOD_PERIODICITY];
                
				$params[]   = $conditions;			

				 //$params[]	= $fields;
				
				$getDataByTimeperiod = $this->CommonInterface->serviceInterface('Timeperiod', 'getDataByParams', $params, $dbConnection);
				// $getDataByTimeperiod  = $this->Timeperiod->getDataByParams( $fields ,$conditions);
				if(isset($getDataByTimeperiod) && count($getDataByTimeperiod)>0)
				{
					$returnData['data']   = $getDataByTimeperiod;
					$returnData['success'] = true;	
						
				}else{
					$returnData['success'] = false;							
					$returnData['message'] = 'No records found';
				}
			   
			}else{
				
				$returnData['success'] = false;
				$returnData['message'] = 'Invalid request';	     //COM005; //'Invalid request'		
			}
			break;			
			
				
			case 302:
				// service for deleting the time period using  any parameters   
				$_POST['TimePeriod']='2060';
			$_POST['periodicity']='C';
		
			if(!empty($_POST['TimePeriod']) || !empty($_POST['periodicity']) || !empty($_POST['EndDate']) || !empty($_POST['StartDate']) || !empty($_POST['TimePeriod_NId'])){
				
				$conditions = array();
				
				if(isset($_POST['TimePeriod']) && !empty($_POST['TimePeriod']))
                $conditions[_TIMEPERIOD_TIMEPERIOD] = trim($_POST['TimePeriod']);	
					
    			if(isset($_POST['periodicity']) && !empty($_POST['periodicity']))
				$conditions[_TIMEPERIOD_PERIODICITY] = trim($_POST['periodicity']);	
			
				if(isset($_POST['StartDate']) && !empty($_POST['StartDate']))
                $conditions[_TIMEPERIOD_STARTDATE] = trim($_POST['StartDate']);	
			
			    if(isset($_POST['EndDate']) && !empty($_POST['EndDate']))
                $conditions[_TIMEPERIOD_ENDDATE] = trim($_POST['EndDate']);	
			
			    if(isset($_POST['TimePeriod_NId']) && !empty($_POST['TimePeriod_NId']))
                $conditions[_TIMEPERIOD_TIMEPERIOD_NID] = trim($_POST['TimePeriod_NId']);	
			    	
	            $params[]   = $conditions;			

				$deleteallTimeperiod = $this->CommonInterface->serviceInterface('Timeperiod', 'deleteByParams', $params, $dbConnection);
                //$deleteallTimeperiod  = $this->Timeperiod->deleteByParams($conditions);
				if($deleteallTimeperiod){
					$returnData['message'] = 'Record deleted successfully';
					$returnData['success'] = true;		
					$returnData['returnvalue'] = $deleteallTimeperiod;						
				}else{
				    $returnData['success'] = false;					
				}				
			}else{				
					$returnData['success'] = false;
					$returnData['message'] = 'Invalid request';	     //COM005; //'Invalid request'		
			}	
			break;	
			
			
			/// cases for saving Time period 
            case 303: 
			// service for saving  details of timeperiod 
				$data = array();			
				$_POST['TimePeriod']='2076.09';
			    $_POST['periodicity']='C';

				if(isset($_POST['TimePeriod']) && !empty($_POST['TimePeriod']))
				$data[_TIMEPERIOD_TIMEPERIOD]   = trim($_POST['TimePeriod']);
			
				if(isset($_POST['periodicity']) && !empty($_POST['periodicity']))
				$data[_TIMEPERIOD_PERIODICITY]  = trim($_POST['periodicity']);
				
				if(isset($_POST['TimePeriod_NId']) && !empty($_POST['TimePeriod_NId']))			
				$data[_TIMEPERIOD_TIMEPERIOD_NID]  = trim($_POST['TimePeriod_NId']);
			
			    $params[]=$data;
					
                $saveTimeperiodDetails = $this->CommonInterface->serviceInterface('Timeperiod', 'insertUpdateDataTimeperiod', $params, $dbConnection);
			   // $saveTimeperiodDetails  = $this->Timeperiod->insertUpdateDataTimeperiod($data);
			   die;
			  	if($saveTimeperiodDetails){				
					$returnData['success']     = true;		
					$returnData['message']     = 'Record inserted successfully!!';
					$returnData['returnvalue'] = $saveTimeperiodDetails;					
				}else{					
					$returnData['success'] = false;							
				}
				
			break;
			
			/// cases for updating  Time period 
            case 304: 
			// service for updating  details of timeperiod 
				
				$data = array();
			
				$_POST['TimePeriod_NId']=12;
				//$_POST['periodicity']='A';
				//$_POST['TimePeriod']='2509';
				
				if(isset($_POST['TimePeriod']) && !empty($_POST['TimePeriod']))
				$data[_TIMEPERIOD_TIMEPERIOD]   = trim($_POST['TimePeriod']);
			
				if(isset($_POST['periodicity']) && !empty($_POST['periodicity']))
				$data[_TIMEPERIOD_PERIODICITY]  = trim($_POST['periodicity']);
				
				if(isset($_POST['TimePeriod_NId']) && !empty($_POST['TimePeriod_NId']))			
				$data[_TIMEPERIOD_TIMEPERIOD_NID]  = trim($_POST['TimePeriod_NId']);
			
				
				$fields = [
                          'TimePeriod'=>'2709',                          
                         ];
                $conditions = $data;

                 //updateDataByParams(array $fields, array $conditions)
			    $params['fields'] = $fields;
			    $params[] = $conditions;
					
                $saveTimeperiodDetails = $this->CommonInterface->serviceInterface('Timeperiod', 'updateDataByParams', $params, $dbConnection);
					
			   // $saveTimeperiodDetails  = $this->Timeperiod->insertUpdateDataTimeperiod($data);
			  	if($saveTimeperiodDetails){				
					$returnData['success']     = true;		
					$returnData['message']     = 'Record inserted successfully!!';
					$returnData['returnvalue'] = $saveTimeperiodDetails;					
				}else{					
					$returnData['success'] = false;							
				}
				pr($returnData);die;
			break;
			
			
			
			// service no. starting with 401 are for subgroup type 
			
			case 401:
			 // service for saving or updating the  subgroup type name 
			 $data = array();
			 
			 if(isset($_POST['Subgroup_Type_Name']) && !empty($_POST['Subgroup_Type_Name']))			 
			 $data[_SUBGROUPTYPE_SUBGROUP_TYPE_NAME]   = $_POST['Subgroup_Type_Name'] ;
			 
			 if(isset($_POST['Subgroup_Type_Order']) && !empty($_POST['Subgroup_Type_Order']))
			 $data[_SUBGROUPTYPE_SUBGROUP_TYPE_ORDER]  = $_POST['Subgroup_Type_Order'];
			 
			 if(isset($_POST['Subgroup_Type_Global']) && !empty($_POST['Subgroup_Type_Global']))
			 $data[_SUBGROUPTYPE_SUBGROUP_TYPE_GLOBAL]  = $_POST['Subgroup_Type_Global'];
		 
			 if(isset($_POST['Subgroup_Type_NId']) && !empty($_POST['Subgroup_Type_NId']))
			 $data[_SUBGROUPTYPE_SUBGROUP_TYPE_NID]  = $_POST['Subgroup_Type_NId'];
		 	
			 if(isset($_POST['Subgroup_Type_GID']) && !empty($_POST['Subgroup_Type_GID']))
             $data[_SUBGROUPTYPE_SUBGROUP_TYPE_GID]    = $_POST['Subgroup_Type_GID'];		    
		     else				 
			 $data[_SUBGROUPTYPE_SUBGROUP_TYPE_GID]    =  $this->Common->guid();
		    
			 $params[]   = $data;			

			 $saveDataforSubgroupType = $this->CommonInterface->serviceInterface('Subgroup', 'insertUpdateDataSubgroupType', $params, $dbConnection);			
			 
			// $saveDataforSubgroupType = $this->Subgroup->insertUpdateDataSubgroupType($data);
			 if($saveDataforSubgroupType){
				 	$returnData['success'] = true;		
					$returnData['message'] = 'Records inserted successfully!!';
					$returnData['returnvalue'] = $saveDataforSubgroupType;				
			 }else{
				 
				 	$returnData['success'] = false;		
			 }
			

            break;
			
			 case 402: 
			// service for updating  details of subgroup type 
				$data = array();
			
				 $_POST['Subgroup_Type_NId']=6;
				// $_POST['Subgroup_Type_Order']='A';
				
				 if(isset($_POST['Subgroup_Type_Name']) && !empty($_POST['Subgroup_Type_Name']))			 
				 $data[_SUBGROUPTYPE_SUBGROUP_TYPE_NAME]   = $_POST['Subgroup_Type_Name'] ;
				 
				 if(isset($_POST['Subgroup_Type_Order']) && !empty($_POST['Subgroup_Type_Order']))
				 $data[_SUBGROUPTYPE_SUBGROUP_TYPE_ORDER]  = $_POST['Subgroup_Type_Order'];
				 
				 if(isset($_POST['Subgroup_Type_Global']) && !empty($_POST['Subgroup_Type_Global']))
				 $data[_SUBGROUPTYPE_SUBGROUP_TYPE_GLOBAL]  = $_POST['Subgroup_Type_Global'];
			 
				 if(isset($_POST['Subgroup_Type_NId']) && !empty($_POST['Subgroup_Type_NId']))
				 $data[_SUBGROUPTYPE_SUBGROUP_TYPE_NID]  = $_POST['Subgroup_Type_NId'];
				
				 if(isset($_POST['Subgroup_Type_GID']) && !empty($_POST['Subgroup_Type_GID']))
				 $data[_SUBGROUPTYPE_SUBGROUP_TYPE_GID]    = $_POST['Subgroup_Type_GID'];		    
			
				
				$fields = [
                          'Subgroup_Type_Name'=>'2029',                          
                         ];
                $conditions = $data;

                 //updateDataByParams(array $fields, array $conditions)
			    $params['fields'] = $fields;
			    $params['conditions'] = $conditions;					
				$saveDataforSubgroupType = $this->CommonInterface->serviceInterface('Subgroup', 'updateDataByParamsSubgroupType', $params, $dbConnection);			
				
			   // $saveTimeperiodDetails  = $this->Timeperiod->insertUpdateDataTimeperiod($data);
			  	if($saveTimeperiodDetails){				
					$returnData['success']     = true;		
					$returnData['message']     = 'Record inserted successfully!!';
					$returnData['returnvalue'] = $saveTimeperiodDetails;					
				}else{					
					$returnData['success'] = false;							
				}
				pr($returnData);die;
			break;
			
			
			
			
			case 403:
				// service for getting the Subgroup type   details on basis of any parameter  
				// passing array $fields, array $conditions			
					
				$conditions = array(); 
			 
			 
				if(isset($_POST['Subgroup_Type_NId']) && !empty($_POST['Subgroup_Type_NId']))
				$conditions[_SUBGROUPTYPE_SUBGROUP_TYPE_NID] = $_POST['Subgroup_Type_NId'];	
					
				if(isset($_POST['Subgroup_Type_Name']) && !empty($_POST['Subgroup_Type_Name']))
				$conditions[_SUBGROUPTYPE_SUBGROUP_TYPE_NAME] = $_POST['Subgroup_Type_Name'];	
			
				if(isset($_POST['Subgroup_Type_GID']) && !empty($_POST['Subgroup_Type_GID']))
				$conditions[_SUBGROUPTYPE_SUBGROUP_TYPE_GID] = $_POST['Subgroup_Type_GID'];	
			
				if(isset($_POST['Subgroup_Type_Order']) && !empty($_POST['Subgroup_Type_Order']))
				$conditions[_SUBGROUPTYPE_SUBGROUP_TYPE_ORDER] = $_POST['Subgroup_Type_Order'];	
			
				if(isset($_POST['Subgroup_Type_Global']) && !empty($_POST['Subgroup_Type_Global']))
				$conditions[_SUBGROUPTYPE_SUBGROUP_TYPE_GLOBAL] = $_POST['Subgroup_Type_Global'];	

				$fields = array();
				
				if(isset($_POST['fields']) && !empty($_POST['fields'])){
					$fields    = $_POST['fields']; 
				}
				$params[]=$fields;
				$params[]=$conditions;
				
				$SubgroupTypeDetails = $this->CommonInterface->serviceInterface('Subgroup', 'getDataByParamsSubgroupType', $params, $dbConnection);			
				
				//$SubgroupTypeDetails   = $this->Subgroup->getDataByParamsSubgroupType( $fields ,$conditions);
				
				if(isset($SubgroupTypeDetails)&& count($SubgroupTypeDetails)>0){
					$returnData['data']  = $SubgroupTypeDetails;
					$returnData['success'] = true;
				}
				else
					$returnData['success'] = false;
					
				
	        
			break;
			
			/*
			
			
			*/
			
				
			case 404:
				
				// service for deleting the subgroup types using  any parameters 
				
				$conditions = array();
				
				if(isset($_POST['Subgroup_Type_NId']) && !empty($_POST['Subgroup_Type_NId']))			    
				$conditions[_SUBGROUPTYPE_SUBGROUP_TYPE_NID] = $_POST['Subgroup_Type_NId'];	
					
				if(isset($_POST['Subgroup_Type_Name']) && !empty($_POST['Subgroup_Type_Name']))
				$conditions[_SUBGROUPTYPE_SUBGROUP_TYPE_NAME] = $_POST['Subgroup_Type_Name'];	
			
				if(isset($_POST['Subgroup_Type_GID']) && !empty($_POST['Subgroup_Type_GID']))
				$conditions[_SUBGROUPTYPE_SUBGROUP_TYPE_GID] = $_POST['Subgroup_Type_GID'];	
			
				if(isset($_POST['Subgroup_Type_Order']) && !empty($_POST['Subgroup_Type_Order']))
				$conditions[_SUBGROUPTYPE_SUBGROUP_TYPE_ORDER] = $_POST['Subgroup_Type_Order'];	
			
				if(isset($_POST['Subgroup_Type_Global']) && !empty($_POST['Subgroup_Type_Global']))
				$conditions[_SUBGROUPTYPE_SUBGROUP_TYPE_GLOBAL] = $_POST['Subgroup_Type_Global'];	
				$params[]=$conditions;
				
				$deleteallSubgroupType = $this->CommonInterface->serviceInterface('Subgroup', 'deleteByParamsSubgroupType', $params, $dbConnection);			
								
    			//$deleteallSubgroupType  = $this->Subgroup->deleteByParamsSubgroupType($conditions);
				if($deleteallSubgroupType>0){
					$returnData['message'] = 'Records deleted successfully';
					$returnData['success'] = true;		
					$returnData['returnvalue'] = $deleteallSubgroupType;				
				}else{
					$returnData['success'] = false;		
				}
			break;	
	
			
			
			
			
			// service no. starting from  501 are for subgroup
			
			case 501:
			// service for saving  subgroup  name 
			if(isset($_POST['Subgroup_Name']) && !empty($_POST['Subgroup_Name'])){
			 // Subgroup_NId is auto increment 
			 $data = array();
			 
			 if(isset($_POST['Subgroup_Name']) && !empty($_POST['Subgroup_Name']))			 
			 $data[_SUBGROUP_SUBGROUP_NAME]   = trim($_POST['Subgroup_Name']) ;
			 
			 if(isset($_POST['Subgroup_Type']) && !empty($_POST['Subgroup_Type']))
			 $data[_SUBGROUP_SUBGROUP_TYPE]  = trim($_POST['Subgroup_Type']);
			 
			 if(isset($_POST['Subgroup_NId']) && !empty($_POST['Subgroup_NId']))
			 $data[_SUBGROUP_SUBGROUP_NID]  = trim($_POST['Subgroup_NId']);
			 
			 if(isset($_POST['Subgroup_GId']) && !empty($_POST['Subgroup_GId']))
			 $data[_SUBGROUP_SUBGROUP_GID]  = trim($_POST['Subgroup_GId']);
			 else
			 $data[_SUBGROUP_SUBGROUP_GID]    =  $this->Common->guid();
		 
		    $params[]=$data;
				
			$saveDataforSubgroupType = $this->CommonInterface->serviceInterface('Subgroup', 'insertUpdateDataSubgroup', $params, $dbConnection);			
				
		     
			 //$saveDataforSubgroupType = $this->Subgroup->insertUpdateDataSubgroup($data);
			 if($saveDataforSubgroupType){
				 $returnData['success'] = true;		
				 $returnData['returnvalue'] = $saveDataforSubgroupType;
				
			 }else{
				  $returnData['success'] = false;					 
			 }
			 //die;
			}

            break;		
			
			
			
			case 502:
			// service for updating the   subgroup  name 
			if(isset($_POST['Subgroup_Name']) && !empty($_POST['Subgroup_Name'])){
			 // Subgroup_NId is auto increment 
			 $data = array();
			 $fields =[_SUBGROUP_SUBGROUP_NAME,_SUBGROUP_SUBGROUP_TYPE];
			 if(isset($_POST['Subgroup_Name']) && !empty($_POST['Subgroup_Name']))			 
			 $data[_SUBGROUP_SUBGROUP_NAME]   = trim($_POST['Subgroup_Name']) ;
			 
			 if(isset($_POST['Subgroup_Type']) && !empty($_POST['Subgroup_Type']))
			 $data[_SUBGROUP_SUBGROUP_TYPE]  = trim($_POST['Subgroup_Type']);
			 
			 if(isset($_POST['Subgroup_NId']) && !empty($_POST['Subgroup_NId']))
			 $data[_SUBGROUP_SUBGROUP_NID]  = trim($_POST['Subgroup_NId']);			 
			 
			 if(isset($_POST['Subgroup_GId']) && !empty($_POST['Subgroup_GId']))
			 $data[_SUBGROUP_SUBGROUP_GID]  = trim($_POST['Subgroup_GId']);		 
		     
			 $params['fields']     = $fields;
             $params['conditions'] = $data;				
			 $saveDataforSubgroupType = $this->CommonInterface->serviceInterface('Subgroup', 'deleteByParamsSubgroupType', $params, $dbConnection);			
				
			 // $saveDataforSubgroupType = $this->Subgroup->insertUpdateDataSubgroup($data);
			 if($saveDataforSubgroupType){
				 $returnData['success'] = true;		
				 $returnData['returnvalue'] = $saveDataforSubgroupType;				
			 }else{
				  $returnData['success'] = false;					 
			 }
			 //die;
			}

            break;
			    
			
			
			case 503:
				// service for getting the Subgroup  details on basis of any parameter  
				// passing array $fields, array $conditions	
				
				 $conditions = array();
			    
				 if(isset($_POST['Subgroup_Name']) && !empty($_POST['Subgroup_Name']))			 
				 $conditions[_SUBGROUP_SUBGROUP_NAME]   = trim($_POST['Subgroup_Name']) ;
				 
				 if(isset($_POST['Subgroup_Type']) && !empty($_POST['Subgroup_Type']))
				 $conditions[_SUBGROUP_SUBGROUP_TYPE]  = trim($_POST['Subgroup_Type']);
				 
				 if(isset($_POST['Subgroup_NId']) && !empty($_POST['Subgroup_NId']))
				 $conditions[_SUBGROUP_SUBGROUP_NID]  = trim($_POST['Subgroup_NId']);				 
				 
				if(isset($_POST['Subgroup_GId']) && !empty($_POST['Subgroup_GId']))
				$conditions[_SUBGROUP_SUBGROUP_GID] = trim($_POST['Subgroup_GId']);							
				
				if(isset($_POST['Subgroup_Global']) && !empty($_POST['Subgroup_Global']))
				$conditions[_SUBGROUP_SUBGROUP_GLOBAL] = trim($_POST['Subgroup_Global']);	
			
				if(isset($_POST['Subgroup_Order']) && !empty($_POST['Subgroup_Order']))
				$conditions[_SUBGROUP_SUBGROUP_ORDER] = trim($_POST['Subgroup_Order']);	

			    $fields = array();
				
				if(isset($_POST['fields']) && !empty($_POST['fields'])){
					$fields    = $_POST['fields']; 
				}
				$params[]=$fields;
				$params[]=$conditions;
                //  $SubgroupDetails   = $this->Subgroup->getDataByParamsSubgroup( $fields ,$conditions);
				
				$SubgroupDetails = $this->CommonInterface->serviceInterface('Subgroup', 'getDataByParamsSubgroup', $params, $dbConnection);			
			
				if(isset($SubgroupDetails)&& count($SubgroupDetails)>0){
					
					$returnData['success'] = true;
					$returnData['data']  = $SubgroupDetails;
				}				
				else
				    $returnData['success'] = false;
					
				
			break;
			
			
				
			case 504:
				// service for deleting the Subgroup Name using  any parameters   
							
				$conditions = array();
			    
				if(isset($_POST['Subgroup_Type']) && !empty($_POST['Subgroup_Type']))
                $conditions[_SUBGROUP_SUBGROUP_TYPE] = trim($_POST['Subgroup_Type']);				
				
				if(isset($_POST['Subgroup_GId']) && !empty($_POST['Subgroup_GId']))
				$conditions[_SUBGROUP_SUBGROUP_GID] = trim($_POST['Subgroup_GId']);				
				
				if(isset($_POST['Subgroup_Order']) && !empty($_POST['Subgroup_Order']))
				$conditions[_SUBGROUP_SUBGROUP_ORDER] = trim($_POST['Subgroup_Order']);	
			
				if(isset($_POST['Subgroup_Global']) && !empty($_POST['Subgroup_Global']))
                $conditions[_SUBGROUP_SUBGROUP_GLOBAL] = trim($_POST['Subgroup_Global']);	
			
			    if(isset($_POST['Subgroup_Name']) && !empty($_POST['Subgroup_Name']))
                $conditions[_SUBGROUP_SUBGROUP_NAME] = trim($_POST['Subgroup_Name']);
			
			    if(isset($_POST['Subgroup_NId']) && !empty($_POST['Subgroup_NId']))
                $conditions[_SUBGROUP_SUBGROUP_NID] = trim($_POST['Subgroup_NId']);
			
                //$deleteallSubgroup  = $this->Subgroup->deleteByParamsSubgroup($conditions);				
				
				$params[]=$conditions;
				
				$deleteallSubgroup = $this->CommonInterface->serviceInterface('Subgroup', 'deleteByParamsSubgroup', $params, $dbConnection);			
			
				if($deleteallSubgroup>0){
					$returnData['message'] = 'Records deleted successfully';
					$returnData['success'] = true;		
					$returnData['returnvalue'] = $deleteallSubgroup;						
				}else{
					$returnData['success'] = false;
				}
			break;	
	
			// service starting with 60 is for bulk upload of subroups 
			case 602:
			
            // service for saving bulk upload data  for subgroup details 
			//require_once(ROOT . DS . 'vendor' . DS  . 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php');
			$filename = WWW_ROOT.DS.'Import IC IUS.xls'; 		
			
			try { 
		
			    $excelDataArray = $this->ExcelReader->loadExcelFile($filename);
			    // $excelDataArray = array_values($data);
				
				$SubgroupTypeordervalue = 1;
				 
				if(isset($excelDataArray['columndetails']) && count($excelDataArray['columndetails'])>0){
				 foreach($excelDataArray['columndetails'] as $subgrptypeExcelIndex => $subgrptypeExcelValue){	
                    // code for subgrouptype save starts here 
					if($subgrptypeExcelIndex >8){
						//echo $subgrpExcelValue;
					    //pr($subgrpExcelIndex);
						 $excelSubgroupTypeDataArray = array();
						 $excelSubgroupTypeDataArray['Subgroup_Type_Name']  = trim($subgrptypeExcelValue);
						 $excelSubgroupTypeDataArray['Subgroup_Type_GID']   = $this->Common->guid();
						 $excelSubgroupTypeDataArray['Subgroup_Type_Order'] = $SubgroupTypeordervalue;
						 $this->request->data  = $excelSubgroupTypeDataArray;
						 $saveDataforSubgroupType = $this->Subgroup->insertDataSubgroupType($this->request->data);
						 $SubgroupTypeordervalue++;
						 
					 }  // closing of  subgrpExcelIndex >8
					    // code for subgrouptype save ends
					}	// end of foreach 			
				} // end of if of subgroup type 				
				// start of the if of subgroup type 
				
				if(isset($excelDataArray['exceldata']) && count($excelDataArray['exceldata'])>0){
			   		foreach($excelDataArray['exceldata'] as $subgrpExcelIndex=>$subgrpExcelValueArray){	
					    pr($subgrpExcelValueArray);
						$subgroupTypeorderindex=0;
						foreach($subgrpExcelValueArray as $subgrpTypename => $subgrpNameExcelValue){	
						// pr($subgrpTypename);
						//  pr($subgrpNameExcelValue);                        
						if($subgroupTypeorderindex>8){ // condition for subgroup type data only
											
						$excelSubgroupDataArray = array();
						$getsubgrouptypeid = $this->Subgroup->getDataBySubgroupTypeName($subgrpTypename);
						
						if(isset($subgrpNameExcelValue) && !empty($subgrpNameExcelValue)){
							
							$excelSubgroupDataArray['Subgroup_Name'] = $subgrpNameExcelValue;					
							$excelSubgroupDataArray['Subgroup_GId']  = $this->Common->guid();	
							$excelSubgroupDataArray['Subgroup_Type'] = $getsubgrouptypeid['Subgroup_Type_NId'];						
							$saveDataforSubgroup = $this->Subgroup->insertDataSubgroup($excelSubgroupDataArray);

					    }
						}	//subgroupTypeorderindex				
						$subgroupTypeorderindex++;
						/*  
					    step 1 check whether the data value exists in 
					    subgroup table or not 
						step 2 get the subgrouptype from subgroup table using function to get id
												  
						
					*/
				   } // end of inner foreach of exceldata  	
				 }  // end of foreach of exceldata  	   
			   }  // end of if of exceldata  
		
			} catch (Exception $e) {  
			   echo 'Exception occured while loading the project list file';  
			   exit;  
			}  
			
				
			break;

            case 701:
                
                //if($this->request->is('post')):
                if(true):
                    $params[]['filename'] = $filename = 'C:\-- Projects --\xls\Temp_Selected_ExcelFile.xls';
                    $returnData = $this->CommonInterface->serviceInterface('CommonInterface', 'bulkUploadXlsOrCsvForIUS', $params, $dbConnection);
                endif;

            break;
		    
			// services for Area
			case 800:
			
			try{
			
			$returnData['success']      = true;
			$returnData['data']['id']   = $this->Auth->user('id');
			// echo json_encode($returnData);
			// die('success');
				
			} catch (Exception $e) {  
			   echo 'Exception occured while loading the project list file';  
			   exit;  
			}
			
            break;
			
			
			case 801: 
			/*$_POST['Area_ID']='IND028021040';
			$_POST['Area_Name']='dhalubaba';
			$_POST['Area_Parent_NId']='24650';
			$_POST['AreaShortName']='dhalubaba'	;
			*/
			//  service for getting the Area details on basis of passed parameters
			if(!empty($_POST['Area_ID']) || !empty($_POST['Area_Name']) || !empty($_POST['Area_GId'])|| !empty($_POST['Area_NId']) || !empty($_POST['Area_Level']) || !empty($_POST['Data_Exist']) || !empty($_POST['AreaShortName'])|| !empty($_POST['Area_Parent_NId'])|| !empty($_POST['Area_Block'])){
				
				$conditions = array();
				
				if(isset($_POST['Area_ID']) && !empty($_POST['Area_ID']))
                $conditions[_AREA_AREA_ID] = trim($_POST['Area_ID']);	
					
    			if(isset($_POST['Area_Name']) && !empty($_POST['Area_Name']))
				$conditions[_AREA_AREA_NAME] = trim($_POST['Area_Name']);	
			
				if(isset($_POST['Area_GId']) && !empty($_POST['Area_GId']))
                $conditions[_AREA_AREA_GID] = trim($_POST['Area_GId']);	
			
			    if(isset($_POST['Area_NId']) && !empty($_POST['Area_NId']))
                $conditions[_AREA_AREA_NID] = trim($_POST['Area_NId']);	
			
			    if(isset($_POST['Area_Level']) && !empty($_POST['Area_Level']))
                $conditions[_AREA_AREA_LEVEL] = trim($_POST['Area_Level']);

			    if(isset($_POST['Data_Exist']) && !empty($_POST['Data_Exist']))
                $conditions[_AREA_DATA_EXIST] = trim($_POST['Data_Exist']);

			    if(isset($_POST['AreaShortName']) && !empty($_POST['AreaShortName']))
                $conditions[_AREA_AREA_SHORT_NAME] = trim($_POST['AreaShortName']);
			   
			    if(isset($_POST['Area_Parent_NId']) && !empty($_POST['Area_Parent_NId']))
                $conditions[_AREA_PARENT_NId] = trim($_POST['Area_Parent_NId']);
			
			    if(isset($_POST['Area_Block']) && !empty($_POST['Area_Block']))
                $conditions[_AREA_AREA_BLOCK] = trim($_POST['Area_Block']);
			
				$params[] = $fields = [_AREA_AREA_BLOCK, _AREA_AREA_SHORT_NAME,_AREA_AREA_ID];
				$params[]=$conditions;		

			    $getAreaDetailsData = $this->CommonInterface->serviceInterface('Area', 'getDataByParams', $params, $dbConnection);
				if($getAreaDetailsData){					
					
					$returnData['success'] = true;	
					$returnData['returnvalue'] = $getAreaDetailsData;			
				}else{	
					$returnData['success'] = false;	
				}							
			}else{
				
				$returnData[] = false;
				$returnData['success'] = false;
				$returnData['message'] = 'Invalid request';	     //COM005; //'Invalid request'		
			}
			
            break;
			
			
				
			case 802:
			
			
				// service for deleting the Area using  any parameters below 
			if(!empty($_POST['Area_ID']) || !empty($_POST['Area_Name']) || !empty($_POST['Area_GId'])|| !empty($_POST['Area_NId']) || !empty($_POST['Area_Level']) || !empty($_POST['Data_Exist']) || !empty($_POST['AreaShortName'])|| !empty($_POST['Area_Parent_NId'])|| !empty($_POST['Area_Block'])){
				
				$conditions = array();
				
				if(isset($_POST['Area_ID']) && !empty($_POST['Area_ID']))
                $conditions[_AREA_AREA_ID] = trim($_POST['Area_ID']);	
					
    			if(isset($_POST['Area_Name']) && !empty($_POST['Area_Name']))
				$conditions[_AREA_AREA_NAME] = trim($_POST['Area_Name']);	
			
				if(isset($_POST['Area_GId']) && !empty($_POST['Area_GId']))
                $conditions[_AREA_AREA_GID] = trim($_POST['Area_GId']);	
			
			    if(isset($_POST['Area_NId']) && !empty($_POST['Area_NId']))
                $conditions[_AREA_AREA_NID] = trim($_POST['Area_NId']);	
			
			    if(isset($_POST['Area_Level']) && !empty($_POST['Area_Level']))
                $conditions[_AREA_AREA_LEVEL] = trim($_POST['Area_Level']);

			    if(isset($_POST['Data_Exist']) && !empty($_POST['Data_Exist']))
                $conditions[_AREA_DATA_EXIST] = trim($_POST['Data_Exist']);

			    if(isset($_POST['AreaShortName']) && !empty($_POST['AreaShortName']))
                $conditions[_AREA_AREA_SHORT_NAME] = trim($_POST['AreaShortName']);
			   
			    if(isset($_POST['Area_Parent_NId']) && !empty($_POST['Area_Parent_NId']))
                $conditions[_AREA_PARENT_NId] = trim($_POST['Area_Parent_NId']);
			    
			
			    if(isset($_POST['Area_Block']) && !empty($_POST['Area_Block']))
                $conditions[_AREA_AREA_BLOCK] = trim($_POST['Area_Block']);			    					
				$params[]=$conditions;
                $deleteallArea = $this->CommonInterface->serviceInterface('Area', 'deleteByParams', $params, $dbConnection);
				if($deleteallArea){
					$returnData['message'] = 'Record deleted successfully';
					$returnData['success'] = true;		
					$returnData['returnvalue'] = $deleteallArea;						
				}else{
				    $returnData['success'] = false;					
				}				
			}else{				
					$returnData['success'] = false;
					$returnData['message'] = 'Invalid request';	     //COM005; //'Invalid request'		
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
			if(!empty($_POST['Area_ID']) || !empty($_POST['Area_Name']) || !empty($_POST['Area_GId'])|| !empty($_POST['Area_NId']) || !empty($_POST['Area_Level']) || !empty($_POST['Data_Exist']) || !empty($_POST['AreaShortName'])|| !empty($_POST['Area_Parent_NId'])|| !empty($_POST['Area_Block'])){
				
				$conditions = array();
				
				if(isset($_POST['Area_ID']) && !empty($_POST['Area_ID']))
                $conditions[_AREA_AREA_ID] = trim($_POST['Area_ID']);	
					
    			if(isset($_POST['Area_Name']) && !empty($_POST['Area_Name']))
				$conditions[_AREA_AREA_NAME] = trim($_POST['Area_Name']);	
			
				if(isset($_POST['Area_GId']) && !empty($_POST['Area_GId']))
                $conditions[_AREA_AREA_GID] = trim($_POST['Area_GId']);
                else
                $conditions[_AREA_AREA_GID] = $this->Common->guid();					
			
			    if(isset($_POST['Area_NId']) && !empty($_POST['Area_NId']))
                $conditions[_AREA_AREA_NID] = trim($_POST['Area_NId']);	
			
			    if(isset($_POST['Area_Level']) && !empty($_POST['Area_Level']))
                $conditions[_AREA_AREA_LEVEL] = trim($_POST['Area_Level']);

			    if(isset($_POST['Data_Exist']) && !empty($_POST['Data_Exist']))
                $conditions[_AREA_DATA_EXIST] = trim($_POST['Data_Exist']);

			    if(isset($_POST['AreaShortName']) && !empty($_POST['AreaShortName']))
                $conditions[_AREA_AREA_SHORT_NAME] = trim($_POST['AreaShortName']);
			   
			    if(isset($_POST['Area_Parent_NId']) && !empty($_POST['Area_Parent_NId']))
                $conditions[_AREA_PARENT_NId] = trim($_POST['Area_Parent_NId']);
			    else
				$conditions[_AREA_PARENT_NId] = '-1';			    	
			
			    if(isset($_POST['Area_Block']) && !empty($_POST['Area_Block']))
                $conditions[_AREA_AREA_BLOCK] = trim($_POST['Area_Block']);			    					
				
				$params[]=$conditions;
                $insertAreadata = $this->CommonInterface->serviceInterface('Area', 'insertUpdateAreaData', $params, $dbConnection);
				if($insertAreadata){
					$returnData['message'] = 'Record saved successfully';
					$returnData['success'] = true;		
					$returnData['returnvalue'] = $insertAreadata;						
				}else{
				    $returnData['success'] = false;					
				}				
			}else{				
					$returnData['success'] = false;
					$returnData['message'] = 'Invalid request';	     //COM005; //'Invalid request'		
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
			if(!empty($_POST['Level_NId']) || !empty($_POST['Area_Level']) || !empty($_POST['Area_Level_Name'])){
							
					
				$conditions = array();
				
				if(isset($_POST['Level_NId']) && !empty($_POST['Level_NId']))
                $conditions[_AREALEVEL_LEVEL_NID] = trim($_POST['Level_NId']);	
					
    			if(isset($_POST['Area_Level']) && !empty($_POST['Area_Level']))
				$conditions[_AREALEVEL_AREA_LEVEL] = trim($_POST['Area_Level']);	
			
				if(isset($_POST['Area_Level_Name']) && !empty($_POST['Area_Level_Name']))
                $conditions[_AREALEVEL_LEVEL_NAME] = trim($_POST['Area_Level_Name']);			   
			
				$params[] = $fields = [_AREALEVEL_LEVEL_NAME, _AREALEVEL_AREA_LEVEL,_AREALEVEL_LEVEL_NID];
				$params[] = $conditions;	

			    $getAreaLevelDetailsData = $this->CommonInterface->serviceInterface('Area', 'getDataByParamsAreaLevel', $params, $dbConnection);
				
				if($getAreaLevelDetailsData){					
					
					$returnData['success'] = true;	
					$returnData['returnvalue'] = $getAreaLevelDetailsData;			
				}else{	
					$returnData['success'] = false;	
				}							
			}else{
				
				$returnData['success'] = false;
				$returnData['message'] = 'Invalid request';	     //COM005; //'Invalid request'		
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
			if(!empty($_POST['Level_NId']) || !empty($_POST['Area_Level']) || !empty($_POST['Area_Level_Name'])){
						
			$conditions = array();
			
			if(isset($_POST['Level_NId']) && !empty($_POST['Level_NId']))
			$conditions[_AREALEVEL_LEVEL_NID] = trim($_POST['Level_NId']);	
				
			if(isset($_POST['Area_Level']) && !empty($_POST['Area_Level']))
			$conditions[_AREALEVEL_AREA_LEVEL] = trim($_POST['Area_Level']);	
		
			if(isset($_POST['Area_Level_Name']) && !empty($_POST['Area_Level_Name']))
			$conditions[_AREALEVEL_LEVEL_NAME] = trim($_POST['Area_Level_Name']);		
										
			$params[]=$conditions;
			$deleteallAreaLevel = $this->CommonInterface->serviceInterface('Area', 'deleteByParamsAreaLevel', $params, $dbConnection);
				if($deleteallAreaLevel){
					$returnData['message'] = 'Record deleted successfully';
					$returnData['success'] = true;		
					$returnData['returnvalue'] = $deleteallAreaLevel;						
				}else{
					$returnData['success'] = false;					
				}				
			}else{				
					$returnData['success'] = false;
					$returnData['message'] = 'Invalid request';	     //COM005; //'Invalid request'		
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
			if(!empty($_POST['Level_NId']) || !empty($_POST['Area_Level']) || !empty($_POST['Area_Level_Name'])){
								
				$conditions = array();
				
				if(isset($_POST['Level_NId']) && !empty($_POST['Level_NId']))
                $conditions[_AREALEVEL_LEVEL_NID] = trim($_POST['Level_NId']);	
					
    			if(isset($_POST['Area_Level']) && !empty($_POST['Area_Level']))
				$conditions[_AREALEVEL_AREA_LEVEL] = trim($_POST['Area_Level']);	
			
				if(isset($_POST['Area_Level_Name']) && !empty($_POST['Area_Level_Name']))
                $conditions[_AREALEVEL_LEVEL_NAME] = trim($_POST['Area_Level_Name']);
			
				$params[]=$conditions;
					
                $insertAreaLeveldata = $this->CommonInterface->serviceInterface('Area', 'insertUpdateAreaLevel', $params, $dbConnection);
			
				if($insertAreaLeveldata){
					$returnData['message'] = 'Record saved successfully';
					$returnData['success'] = true;		
					$returnData['returnvalue'] = $insertAreaLeveldata;						
				}else{
				    $returnData['success'] = false;					
				}				
			}else{				
					$returnData['success'] = false;
					$returnData['message'] = 'Invalid request';	     //COM005; //'Invalid request'		
			}	
			pr($returnData);
			die;
			break;
			
			case 904:		
			// service for bulk upload of area excel sheet
			  //if($this->request->is('post')):
                if(true):
                    $params[]['filename'] = $filename = 'C:\wamp\www\dfa_devinfo_data_admin\webroot\data-import-formats\Area.xls';
                    //$returnData = $this->CommonInterface->bulkUploadXlsOrCsvForIndicator($params);                    
                    $returnData = $this->CommonInterface->serviceInterface('CommonInterface', 'bulkUploadXlsOrCsvForArea', $params, $dbConnection);   
die;					
                endif;
                
                break;
			
                
              
			
			
			
        
			endswitch;
			

        	return $this->service_response($returnData, $convertJson);

		
	}// service query ends here 
	
	
	/*
	 function to get data base details on basis of dbId 
	 @params $dbId is the database id of database 
	 
	*/
	
	public function getDbDetails($dbId=null){
	
		/*$this->MSystemConfirgurations = TableRegistry::get('MSystemConfirgurations');
        $configIsDefDB = $this->MSystemConfirgurations->findByKey('DEVINFO_DBID');
	    if($configIsDefDB) {
		   $this->MDatabaseConnections = TableRegistry::get('MDatabaseConnections');
		   $databasedetails = $this->MDatabaseConnections->getDbNameByID($configIsDefDB);
	    }
		return $databasedetails;
		*/
			
	}
		 

	// - METHOD TO GET RETURN DATA
	// - METHOD TO GET RETURN DATA
	public function service_response($data, $convertJson='_YES') {	
				
		$data['IsAuthenticated'] = false;
		if ($this->Auth->user('id')) {
			$data['IsAuthenticated'] = true;
		}
		if($convertJson == '_YES') {
			$data = json_encode($data);
		}		
		if (!$this->request->is('requested')) {
            $this->response->body($data);
            return $this->response;
        }else{
            return $data;
		}
		
	}
	

   } 
