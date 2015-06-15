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

/**
 * Services Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class ServicesController extends AppController
{
    //Loading Components
     
	public $components = ['Indicator', 'Unit', 'Timeperiod','Subgroup','Common','ExcelReader'];
    //public $components = ['Indicator', 'Unit', 'Timeperiod','Subgroup','Common'];

    /**
	* 
	* @return JSON/boolean
	* @throws NotFoundException When the view file could not be found
	*	or MissingViewException in debug mode.
	*/
    public function serviceQuery($case = null)
    {
        $this->autoRender = false;
        $this->layout = '';
		$convertJson = '_YES';
		$returnData = [];
        
        switch($case):

            case 101: //Select Data using Indicator_NId -- Indicator table

                //getDataByIds($ids = null, $fields = [], $type = 'all' )
                $returnData = $this->Indicator->getDataByIds([383,384,386]); 
                break;

            case 102: //Select Data using Conditions -- Indicator table
                
                $fields = [_INDICATOR_INDICATOR_NAME, _INDICATOR_INDICATOR_INFO];
                $conditions = [_INDICATOR_INDICATOR_GID.' IN'=>['POPDEN', 'AREA']];
                
                //getDataByParams(array $fields, array $conditions)
                $returnData = $this->Indicator->getDataByParams($fields, $conditions); 
                break;

            case 103: //Delete Data using Indicator_NId -- Indicator table
                
                //deleteByIds($ids = null)
                $returnData = $this->Indicator->deleteByIds([383,384,385]); 
                break;

            case 104: //Delete Data using Conditions -- Indicator table
                
                $conditions = [_INDICATOR_INDICATOR_GID.' IN'=>['TEST_GID', 'TEST_GID2']];

                //deleteByParams(array $conditions)
                $returnData = $this->Indicator->deleteByParams($conditions); 
                break;

            case 105: //Insert New Data -- Indicator table
                
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

                if($this->request->is('post')):
                    //insertData(array $fieldsArray = $this->request->data)
                    $returnData = $this->Indicator->insertData($this->request->data);
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
                    $returnData = $this->Indicator->updateDataByParams($fields, $conditions);
                endif;

                break;
                
            case 107: //Bulk Insert/Update Data -- Indicator table
                
                //if($this->request->is('post')):
                    //The following line should do the same like App::import() in the older version of cakePHP
                    require_once(ROOT . DS . 'vendor' . DS  . 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php');

                    $filename = 'C:\-- Projects --\Indicator.xls';
                    $insertFieldsArr = [];
                    $insertDataArr = [];
                    $insertDataNames = [];
                    $insertDataGids = [];
                    $insertDataKeys = [_INDICATOR_INDICATOR_NAME, _INDICATOR_INDICATOR_GID, _INDICATOR_HIGHISGOOD];

                    $objPHPExcel = \PHPExcel_IOFactory::load($filename);
                
                    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                        $worksheetTitle     = $worksheet->getTitle();
                        $highestRow         = $worksheet->getHighestRow(); // e.g. 10
                        $highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
                        $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);
                    
                        for ($row = 1; $row <= $highestRow; ++ $row) {

                            for ($col = 0; $col < $highestColumnIndex; ++ $col) {
                                $cell = $worksheet->getCellByColumnAndRow($col, $row);
                                $val = $cell->getValue();
                                $dataType = \PHPExcel_Cell_DataType::dataTypeForValue($val);
                            
                                if($row >= 6){                                
                                    $insertDataArr[$row][] = $val;
                                }else{
                                    continue;
                                }

                                /*
                                if($row == 1){
                                    $insertFieldsArr[] = $val;
                                }else{
                                    $insertDataArr[$row][$insertFieldsArr[$col]] = $val;
                                }*/
                            }

                            if(isset($insertDataArr[$row])):
                            
                                $insertDataArr[$row] = array_combine($insertDataKeys, $insertDataArr[$row]);
                                $insertDataArr[$row] = array_filter($insertDataArr[$row]);

                                //We don't need this row if the name field is empty
                                if(!isset($insertDataArr[$row][_INDICATOR_INDICATOR_NAME])){
                                    unset($insertDataArr[$row]);
                                }else if(!isset($insertDataArr[$row][_INDICATOR_INDICATOR_GID])){
                                    $insertDataNames[] = $insertDataArr[$row][_INDICATOR_INDICATOR_NAME];
                                }else{
                                    $insertDataGids[] = $insertDataArr[$row][_INDICATOR_INDICATOR_GID];
                                }

                            endif;

                        }
                    }
                
                    $dataArray = array_values(array_filter($insertDataArr));

                    //insertOrUpdateBulkData(array $dataArray = $this->request->data)
                    //$returnData = $this->Indicator->insertOrUpdateBulkData($dataArray);

                    //Get Indicator Ids based on Indicator Name
                    if(!empty($insertDataNames)){
                        //getDataByName(array $dataArray = $this->request->data)
                        //$returnData = $this->Indicator->getDataByName($dataArray);
                    }

                    //Get Indicator Ids based on Indicator GID
                    //insertOrUpdateBulkData(array $dataArray = $this->request->data)
                    //$returnData = $this->Indicator->insertOrUpdateBulkData($dataArray);

                    //Update Indicator based on Indicator Name
                    //insertOrUpdateBulkData(array $dataArray = $this->request->data)
                    //$returnData = $this->Indicator->insertOrUpdateBulkData($dataArray);

                    //Update Indicator based on Indicator GID
                    //insertOrUpdateBulkData(array $dataArray = $this->request->data)
                    //$returnData = $this->Indicator->insertOrUpdateBulkData($dataArray);

                //endif;

                break;

            case 201: //Select Data using Unit_NId -- Unit table

                //getDataByIds($ids = null, $fields = [], $type = 'all' )
                $returnData = $this->Unit->getDataByIds([10,41]); 
                break;

            case 202: //Select Data using Conditions -- Unit table
                
                $fields = ['Unit_Name', 'Unit_Global'];
                $conditions = ['Unit_GId IN'=>['POPDEN', 'AREA']];

                //getDataByParams(array $fields, array $conditions)
                $returnData = $this->Unit->getDataByParams($fields, $conditions); 
                break;

            case 203: //Delete Data using Unit_NId -- Unit table
                
                //deleteByIds($ids = null)
                $returnData = $this->Unit->deleteByIds([42]); 
                break;

            case 204: //Delete Data using Conditions -- Unit table
                
                $conditions = ['Unit_GId IN'=>['SOME_001_TEST', 'SOME_003_TEST']];

                //deleteByParams(array $conditions)
                $returnData = $this->Unit->deleteByParams($conditions);

            case 205: //Insert New Data -- Unit table
                
                $this->request->data = [
                                    'Unit_NId'=>'43',
                                    'Unit_Name'=>'Custom_test_name',
                                    'Unit_GId'=>'SOME_002_TEST',
                                    'Unit_Global'=>'0'
                                    ];

                if($this->request->is('post')):
                    //insertData(array $fieldsArray = $this->request->data)
                    $returnData = $this->Unit->insertData($this->request->data);
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
                    $returnData = $this->Unit->updateDataByParams($fields, $conditions);
                endif;

            break;

            case 207: //Bulk Insert/Update Data -- Unit table
                
                //The following line should do the same like App::import() in the older version of cakePHP
                require_once(ROOT . DS . 'vendor' . DS  . 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php');

                $filename = 'C:\-- Projects --\Bulk_unit_test_file.xlsx';
                $insertFieldsArr = [];
                $insertDataArr = [];

                $objPHPExcel = \PHPExcel_IOFactory::load($filename);
                
                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                    $worksheetTitle     = $worksheet->getTitle();
                    $highestRow         = $worksheet->getHighestRow(); // e.g. 10
                    $highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
                    $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);
                    
                    for ($row = 1; $row <= $highestRow; ++ $row) {

                        for ($col = 0; $col < $highestColumnIndex; ++ $col) {
                            $cell = $worksheet->getCellByColumnAndRow($col, $row);
                            $val = $cell->getValue();
                            $dataType = \PHPExcel_Cell_DataType::dataTypeForValue($val);
                            
                            if($row == 1){
                                $insertFieldsArr[] = $val;
                            }else{
                                $insertDataArr[$row][$insertFieldsArr[$col]] = $val;
                            }
                        }

                    }
                }
                
                $dataArray = array_values($insertDataArr);

                if($this->request->is('post')):
                    //insertOrUpdateBulkData(array $Indicator = $this->request->data)
                    $returnData = $this->Unit->insertOrUpdateBulkData($dataArray);
                endif;

            break;

           
			// nos starting with 301 are for timeperiod
			
			// Read  cases of Timeperiod 
			
				
			case 301:
				// service for getting the Timeperiod details on basis of ids
				// can be one or multiple in form of array 
				// parameters  $ids, array $fields ,$type default is all
				$ids     = array(2,3);
				$fields  = array(_TIMEPERIOD_TIMEPERIOD_NID,_TIMEPERIOD_TIMEPERIOD); // fields can be blank also 
				$ids     = [2,3];
				if(isset($_REQUEST['ids']) && !empty($_REQUEST['ids'])){
					$ids   = $_REQUEST['ids']; 
				}
				
				if(isset($_REQUEST['fields']) && !empty($_REQUEST['fields'])){
					$fields    = $_REQUEST['fields']; 
				}
				//$fields  = array(_SUBGROUPTYPE_SUBGROUP_TYPE_NAME,_SUBGROUPTYPE_SUBGROUP_TYPE_NID); // fields can be blank also 
				//$type    = 'list'; // type can be list or all only 
				$type    = '';
				if(isset($_REQUEST['type']) && !empty($_REQUEST['type'])){
					$type    = $_REQUEST['type']; 
				}
				
				$getDataByTimeperiod  = $this->Timeperiod->getDataByIds($ids,$fields,$type);
				if(isset($getDataByTimeperiod) && count($getDataByTimeperiod)>0)
				{
					$returnData['data']   = $getDataByTimeperiod;
					$returnData['success'] = true;	
						
				}else{
					$returnData['success'] = false;							
					$returnData['message'] = 'No records found';
				}
            
			break;	
			
			
				
			case 302:
			//  service for getting the Timeperiod details on basis of Timeperiod 
			//  passing  $TimePeriodvalue as Timeperiod value ,  $periodicity is optional
			if(isset($_REQUEST['TimePeriodvalue']) && !empty($_REQUEST['TimePeriodvalue'])){
				
				$TimePeriodvalue       = trim($this->request->query['TimePeriodvalue']);			
				$Periodicityvalue      = trim($this->request->query['periodicity']);			
                $getDataByTimeperiod   = $this->Timeperiod->getDataByTimeperiod($TimePeriodvalue,$Periodicityvalue);
                if(isset($getDataByTimeperiod) && count($getDataByTimeperiod)>0)
				{
					$returnData['data']   = $getDataByTimeperiod;
					$returnData['success'] = true;	
						
				}else{
					$returnData['success'] = false;							
					$returnData['message'] = 'No records found';
				}
            				
				
			}else{				
				$returnData[] = false;
				$returnData['success'] = false;
				$returnData['message'] = 'Invalid request';	     //COM005; //'Invalid request'		
			}  
			break;

			
			
			case 303:
				// service for getting the Timeperiod details on basis of any parameter  
				// passing array $fields, array $conditions
			
			if(!empty($_REQUEST['TimePeriod']) || !empty($_REQUEST['periodicity']) || !empty($_REQUEST['EndDate']) || !empty($_REQUEST['StartDate']) || !empty($_REQUEST['TimePeriod_NId'])){
				
				$conditions = array();
			    
				if(isset($_REQUEST['TimePeriod']) && !empty($_REQUEST['TimePeriod']))
                $conditions[_TIMEPERIOD_TIMEPERIOD] = trim($this->request->query['TimePeriod']);	
					
    			if(isset($_REQUEST['periodicity']) && !empty($_REQUEST['periodicity']))
				$conditions[_TIMEPERIOD_PERIODICITY] = trim($this->request->query['periodicity']);	
			
				if(isset($_REQUEST['StartDate']) && !empty($_REQUEST['StartDate']))
                $conditions[_TIMEPERIOD_STARTDATE] = trim($this->request->query['StartDate']);	
			
			    if(isset($_REQUEST['EndDate']) && !empty($_REQUEST['EndDate']))
                $conditions[_TIMEPERIOD_ENDDATE] = trim($this->request->query['EndDate']);	
			
			    if(isset($_REQUEST['TimePeriod_NId']) && !empty($_REQUEST['TimePeriod_NId']))
                $conditions[_TIMEPERIOD_TIMEPERIOD_NID] = trim($this->request->query['TimePeriod_NId']);	

			    $fields = array();
				
                $getDataByTimeperiod  = $this->Timeperiod->getDataByParams( $fields ,$conditions);
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
			
			
			// Delete cases of Time period 
			case 304:
				// service for deleting the Time period using  Time period value 
			if(isset($_REQUEST['TimePeriodvalue']) && !empty($_REQUEST['TimePeriodvalue'])){
			
				$TimePeriodvalue = trim($this->request->query['TimePeriodvalue']);			
                $deleteByTimeperiod  = $this->Timeperiod->deleteByTimePeriod($TimePeriodvalue);			   
			    if($deleteByTimeperiod){
					
					$returnData['message'] = 'Record deleted successfully';
					$returnData['success'] = true;	
					$returnData['returnvalue'] = $deleteByTimeperiod;			
				}else{	
					$returnData['success'] = false;	
				}							
			}else{
				
				$returnData[] = false;
				$returnData['success'] = false;
				$returnData['message'] = 'Invalid request';	     //COM005; //'Invalid request'		
			}
			
            break;
			
			
				
			case 305:
				// service for deleting the Time period using  id it can be one  or mutiple  
			if(isset($_REQUEST['TimePeriodids']) && !empty($_REQUEST['TimePeriodids'])){
			    //$TimePeriodids = trim($this->request->query['TimePeriodids']);			
				$ids  = [4,5];			
                $deleteallTimeperiodIDS   = $this->Timeperiod->deleteByIds($ids);
			    if($deleteallTimeperiodIDS){
					
					$returnData['message'] = 'Record deleted successfully';
					$returnData['success'] = true;		
					$returnData['returnvalue'] = $deleteallTimeperiodIDS;
					
				}else{					
					$returnData['success'] = false;	
				}
			}else{				
			
				$returnData['success'] = false;
				$returnData['message'] = 'Invalid request';	     //COM005; //'Invalid request'		
			}
			
            break;			
			
			
				
			case 306:
				// service for deleting the time period using  any parameters   
			if(!empty($_REQUEST['TimePeriod']) || !empty($_REQUEST['periodicity']) || !empty($_REQUEST['EndDate']) || !empty($_REQUEST['StartDate']) || !empty($_REQUEST['TimePeriod_NId'])){
			    //$TimePeriodids = $this->request->query['TimePeriodids'];			
				$conditions = array();
				
				if(isset($_REQUEST['TimePeriod']) && !empty($_REQUEST['TimePeriod']))
                $conditions[_TIMEPERIOD_TIMEPERIOD] = trim($this->request->query['TimePeriod']);	
					
    			if(isset($_REQUEST['periodicity']) && !empty($_REQUEST['periodicity']))
				$conditions[_TIMEPERIOD_PERIODICITY] = trim($this->request->query['periodicity']);	
			
				if(isset($_REQUEST['StartDate']) && !empty($_REQUEST['StartDate']))
                $conditions[_TIMEPERIOD_STARTDATE] = trim($this->request->query['StartDate']);	
			
			    if(isset($_REQUEST['EndDate']) && !empty($_REQUEST['EndDate']))
                $conditions[_TIMEPERIOD_ENDDATE] = trim($this->request->query['EndDate']);	
			
			    if(isset($_REQUEST['TimePeriod_NId']) && !empty($_REQUEST['TimePeriod_NId']))
                $conditions[_TIMEPERIOD_TIMEPERIOD_NID] = trim($this->request->query['TimePeriod_NId']);	
			    					
                $deleteallTimeperiod  = $this->Timeperiod->deleteByParams($conditions);
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
            case 307: 
			// service for saving  details of timeperiod 
				$data = array();
				
				$_REQUEST['TimePeriodData']=2070;
				$_REQUEST['Periodicity']='D';
				$_REQUEST['TimePeriod_NId']=8;
				
				if(isset($_REQUEST['TimePeriodData']) && !empty($_REQUEST['TimePeriodData']))
				$data[_TIMEPERIOD_TIMEPERIOD]   = trim($_REQUEST['TimePeriodData']);
			
				if(isset($_REQUEST['Periodicity']) && !empty($_REQUEST['Periodicity']))
				$data[_TIMEPERIOD_PERIODICITY]  = trim($_REQUEST['Periodicity']);
				
				if(isset($_REQUEST['TimePeriod_NId']) && !empty($_REQUEST['TimePeriod_NId']))			
				$data[_TIMEPERIOD_TIMEPERIOD_NID]  = trim($_REQUEST['TimePeriod_NId']);
			
			    $saveTimeperiodDetails  = $this->Timeperiod->insertUpdateDataTimeperiod($data);
			  	if($saveTimeperiodDetails){				
					$returnData['success']     = true;		
					$returnData['message']     = 'Record inserted successfully!!';
					$returnData['returnvalue'] = $saveTimeperiodDetails;					
				}else{					
					$returnData['success'] = false;							
				}
				
			break;
			
			
			
			// service no. starting with 401 are for subgroup type 
			
			case 401:
			 // service for saving or updating the  subgroup type name 
			 // if(isset($_REQUEST['subgrouptypename']) && !empty($_REQUEST['subgrouptypename'])){
			 $data = array();
			 $_REQUEST['Subgroup_Type_NId']  = 223;
			 $_REQUEST['Subgroup_Type_Name'] = 'Subgroup_Type_Name78';
			 
			 if(isset($_REQUEST['Subgroup_Type_Name']) && !empty($_REQUEST['Subgroup_Type_Name']))			 
			 $data[_SUBGROUPTYPE_SUBGROUP_TYPE_NAME]   = $_REQUEST['Subgroup_Type_Name'] ;
			 
			 if(isset($_REQUEST['Subgroup_Type_Order']) && !empty($_REQUEST['Subgroup_Type_Order']))
			 $data[_SUBGROUPTYPE_SUBGROUP_TYPE_ORDER]  = $_REQUEST['Subgroup_Type_Order'];
			 
			 if(isset($_REQUEST['Subgroup_Type_Global']) && !empty($_REQUEST['Subgroup_Type_Global']))
			 $data[_SUBGROUPTYPE_SUBGROUP_TYPE_GLOBAL]  = $_REQUEST['Subgroup_Type_Global'];
		 
			 if(isset($_REQUEST['Subgroup_Type_NId']) && !empty($_REQUEST['Subgroup_Type_NId']))
			 $data[_SUBGROUPTYPE_SUBGROUP_TYPE_NID]  = $_REQUEST['Subgroup_Type_NId'];
		 		 
			 $data[_SUBGROUPTYPE_SUBGROUP_TYPE_GID]    =  $this->Common->guid();
			 
			 $saveDataforSubgroupType = $this->Subgroup->insertUpdateDataSubgroupType($data);
			 if($saveDataforSubgroupType){
				 	$returnData['success'] = true;		
					$returnData['message'] = 'Record inserted successfully!!';
					$returnData['returnvalue'] = $saveDataforSubgroupType;
				
			 }else{
				 
				 	$returnData['success'] = false;		
			 }
			 //pr($saveDataforSubgroupType);
			 //die;
			//}

            break;
			
			
			case 402:
			// service for getting the subgroup   details on basis of ids
			// can be one or multiple in form of array // passing  $ids, array $fields ,$type default is all
		
				$ids     = [2,3];
				//$fields  = array(_SUBGROUPTYPE_SUBGROUP_TYPE_NID,_SUBGROUPTYPE_SUBGROUP_TYPE_NAME); // fields can be blank also 
				//$type    = 'list'; // type can be list or all only 
				if(isset($_REQUEST['ids']) && !empty($_REQUEST['ids'])){
					$ids   = $_REQUEST['ids']; 
				}
				
				if(isset($_REQUEST['fields']) && !empty($_REQUEST['fields'])){
					$fields    = $_REQUEST['fields']; 
				}
				if(isset($_REQUEST['type']) && !empty($_REQUEST['type'])){
					$type    = $_REQUEST['type']; 
				}
				
				$SubgrouptypeDetails  = $this->Subgroup->getDataByIdsSubgroupType($ids,$fields,$type);
				if(isset($SubgrouptypeDetails) && count($SubgrouptypeDetails)>0){
					$returnData['data']   = $SubgrouptypeDetails;
					$returnData['success'] = true;	
						
				}else{
					$returnData['success'] = false;	
					
				}
			
		    break;
			
			
			case 403:
			//  service for getting the subgroup  details on basis of subgroup name  
			//  passing  $subgrouptypename
			if(isset($_REQUEST['subgrouptypename']) && !empty($_REQUEST['subgrouptypename'])){
				
				$subgrouptypevalue         = trim($this->request->query['subgrouptypename']);			
                $SubgroupTypeDetails       = $this->Subgroup->getDataBySubgroupTypeName($subgrouptypevalue);
			    if(isset($SubgroupTypeDetails) && count($SubgroupTypeDetails)>0){
					$returnData['data']        = $SubgroupTypeDetails;
					$returnData['success'] = true;							
				}else{
					$returnData['success'] = false;				
				}				
			}else{
    			$returnData['success'] = false;
				$returnData['message'] = 'Invalid request';	     //COM005; //'Invalid request'		
			}  
			break;
			
			case 404:
				// service for getting the Subgroup type   details on basis of any parameter  
				// passing array $fields, array $conditions			
					
				$conditions = array(); 
			 
			 
				if(isset($_REQUEST['Subgroup_Type_NId']) && !empty($_REQUEST['Subgroup_Type_NId']))
				$conditions[_SUBGROUPTYPE_SUBGROUP_TYPE_NID] = $this->request->query['Subgroup_Type_NId'];	
					
				if(isset($_REQUEST['Subgroup_Type_Name']) && !empty($_REQUEST['Subgroup_Type_Name']))
				$conditions[_SUBGROUPTYPE_SUBGROUP_TYPE_NAME] = $this->request->query['Subgroup_Type_Name'];	
			
				if(isset($_REQUEST['Subgroup_Type_GID']) && !empty($_REQUEST['Subgroup_Type_GID']))
				$conditions[_SUBGROUPTYPE_SUBGROUP_TYPE_GID] = $this->request->query['Subgroup_Type_GID'];	
			
				if(isset($_REQUEST['Subgroup_Type_Order']) && !empty($_REQUEST['Subgroup_Type_Order']))
				$conditions[_SUBGROUPTYPE_SUBGROUP_TYPE_ORDER] = $this->request->query['Subgroup_Type_Order'];	
			
				if(isset($_REQUEST['Subgroup_Type_Global']) && !empty($_REQUEST['Subgroup_Type_Global']))
				$conditions[_SUBGROUPTYPE_SUBGROUP_TYPE_GLOBAL] = $this->request->query['Subgroup_Type_Global'];	

				$fields = array();
				
				if(isset($_REQUEST['fields']) && !empty($_REQUEST['fields'])){
					$fields    = $_REQUEST['fields']; 
				}
				
				$SubgroupTypeDetails   = $this->Subgroup->getDataByParamsSubgroupType( $fields ,$conditions);
				
				if(isset($SubgroupTypeDetails)&& count($SubgroupTypeDetails)>0){
					$returnData['data']  = $SubgroupTypeDetails;
					$returnData['success'] = true;
				}
				else
				$returnData['success'] = false;
					
				
	        
			break;
			
			
			// Delete cases of Subgroup type 
			case 405:
				// service for deleting the Subgroup Type using   Subgroup Type Name  value 
			if(isset($_REQUEST['Subgroup_Type_Name']) && !empty($_REQUEST['Subgroup_Type_Name'])){
			
				$SubgroupTypeNamevalue = trim($this->request->query['Subgroup_Type_Name']);			
                $deleteBySubgrouptypeName  = $this->Subgroup->deleteBySubgroupTypeName($SubgroupTypeNamevalue);			   
			    if($deleteBySubgrouptypeName){
					$returnData['message'] = 'Record deleted successfully';
					$returnData['success'] = true;	
					$returnData['returnvalue'] = $deleteBySubgrouptypeName;						
				}else{
					$returnData['success'] = false;					
				}
			}else{				
				$returnData['success'] = false;
				$returnData['message'] = 'Invalid request';	     //COM005; //'Invalid request'		
			}
			
            break;
			
			
				
			case 406:
			
			   // service for deleting the Subgroup types using subgroup type nids it can be one  or mutiple  
			if(isset($_REQUEST['Subgroupids']) && !empty($_REQUEST['Subgroupids'])){
				$ids  = [223,215];			
                $deletebySubgroupIDS   = $this->Subgroup->deleteByIdsSubgroupType($ids);
			    if($deletebySubgroupIDS){
					 $returnData['message'] = 'Record deleted successfully!!';
					 $returnData['success'] = true;		
					 $returnData['returnvalue'] = $deletebySubgroupIDS;	
				}else{
					$returnData['success'] = false;		
				}
			}else{				
				
				$returnData['success'] = false;
				$returnData['message'] = 'Invalid request';	     //COM005; //'Invalid request'		
			}
			
            break;			
			
			
				
			case 407:
				
				// service for deleting the subgroup types using  any parameters 
				
				$conditions = array();
				
				if(isset($_REQUEST['Subgroup_Type_NId']) && !empty($_REQUEST['Subgroup_Type_NId']))			    
				$conditions[_SUBGROUPTYPE_SUBGROUP_TYPE_NID] = $this->request->query['Subgroup_Type_NId'];	
					
				if(isset($_REQUEST['Subgroup_Type_Name']) && !empty($_REQUEST['Subgroup_Type_Name']))
				$conditions[_SUBGROUPTYPE_SUBGROUP_TYPE_NAME] = $this->request->query['Subgroup_Type_Name'];	
			
				if(isset($_REQUEST['Subgroup_Type_GID']) && !empty($_REQUEST['Subgroup_Type_GID']))
				$conditions[_SUBGROUPTYPE_SUBGROUP_TYPE_GID] = $this->request->query['Subgroup_Type_GID'];	
			
				if(isset($_REQUEST['Subgroup_Type_Order']) && !empty($_REQUEST['Subgroup_Type_Order']))
				$conditions[_SUBGROUPTYPE_SUBGROUP_TYPE_ORDER] = $this->request->query['Subgroup_Type_Order'];	
			
				if(isset($_REQUEST['Subgroup_Type_Global']) && !empty($_REQUEST['Subgroup_Type_Global']))
				$conditions[_SUBGROUPTYPE_SUBGROUP_TYPE_GLOBAL] = $this->request->query['Subgroup_Type_Global'];	

    			$deleteallSubgroupType  = $this->Subgroup->deleteByParamsSubgroupType($conditions);
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
			if(isset($_REQUEST['Subgroup_Name']) && !empty($_REQUEST['Subgroup_Name'])){
			 // Subgroup_NId is auto increment 
			 $data = array();
			 
			 if(isset($_REQUEST['Subgroup_Name']) && !empty($_REQUEST['Subgroup_Name']))			 
			 $data[_SUBGROUP_SUBGROUP_NAME]   = trim($_REQUEST['Subgroup_Name']) ;
			 
			 if(isset($_REQUEST['Subgroup_Type']) && !empty($_REQUEST['Subgroup_Type']))
			 $data[_SUBGROUP_SUBGROUP_TYPE]  = trim($_REQUEST['Subgroup_Type']);
			 
			 if(isset($_REQUEST['Subgroup_NId']) && !empty($_REQUEST['Subgroup_NId']))
			 $data[_SUBGROUP_SUBGROUP_NID]  = trim($_REQUEST['Subgroup_NId']);
			 
			 
			 $data[_SUBGROUP_SUBGROUP_GID]    =  $this->Common->guid();
		     
			 $saveDataforSubgroupType = $this->Subgroup->insertUpdateDataSubgroup($data);
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
			// service for getting the subgroup   details on basis of ids
			// can be one or multiple in form of array // passing  $ids, array $fields ,$type default is all
			if($this->request->is('post')){
		
				$ids     = [2,3];
				if(isset($_REQUEST['ids']) && !empty($_REQUEST['ids'])){
					$ids   = array();
					$ids   = $_REQUEST['ids']; 
				}
				
				if(isset($_REQUEST['fields']) && !empty($_REQUEST['fields'])){
					$fields  = array();
					$fields    = $_REQUEST['fields']; 
				}
				//$fields  = array('Subgroup_NId','Subgroup_Name'); // fields can be blank also 
				//$type    = ''; // type can be list or all only 
				if(isset($_REQUEST['type']) && !empty($_REQUEST['type'])){
					$type    = $_REQUEST['type']; 
				}
				
				$SubgroupDetails  = $this->Subgroup->getDataByIdsSubgroup($ids,$fields,$type);
				if(isset($SubgroupDetails)&& count($SubgroupDetails)>0){
					$returnData['data'] = $SubgroupDetails;
					$returnData['success'] = true;	
						
				}else{
					$returnData['success'] = false;	
				}
			}else{
				$returnData[] = false;
				$returnData['success'] = false;
				$returnData['message'] = 'Invalid request';	     //COM005; //'Invalid request'		
			}  
			break;	
			
			
				
			case 503:
			//  service for getting the subgroup  details on basis of subgroup name  
			//  passing  $subgroupvalue
			if(isset($_REQUEST['subgroup']) && !empty($_REQUEST['subgroup'])){
				
				$subgroupvalue         = trim($this->request->query['subgroup']);			
                $SubgroupDetails       = $this->Subgroup->getDataBySubgroupName($subgroupvalue);
			    if(isset($SubgroupDetails)&& count($SubgroupDetails)>0){					
					$returnData['data'] = $SubgroupDetails;
					$returnData['success'] = true;				
				}else{
					$returnData['success'] = false;	
				}
			}else{				
				$returnData['success'] = false;
				$returnData['message'] = 'Invalid request';	     //COM005; //'Invalid request'		
			}  
			break;

			
			
			case 504:
				// service for getting the Subgroup  details on basis of any parameter  
				// passing array $fields, array $conditions			
				//if(isset($_REQUEST['TimePeriod']) && !empty($_REQUEST['TimePeriod'])){
				
				$conditions = array();
			    
				 if(isset($_REQUEST['Subgroup_Name']) && !empty($_REQUEST['Subgroup_Name']))			 
				 $conditions[_SUBGROUP_SUBGROUP_NAME]   = trim($_REQUEST['Subgroup_Name']) ;
				 
				 if(isset($_REQUEST['Subgroup_Type']) && !empty($_REQUEST['Subgroup_Type']))
				 $conditions[_SUBGROUP_SUBGROUP_TYPE]  = trim($_REQUEST['Subgroup_Type']);
				 
				 if(isset($_REQUEST['Subgroup_NId']) && !empty($_REQUEST['Subgroup_NId']))
				 $conditions[_SUBGROUP_SUBGROUP_NID]  = trim($_REQUEST['Subgroup_NId']);
				 
				 
				if(isset($_REQUEST['Subgroup_GId']) && !empty($_REQUEST['Subgroup_GId']))
				$conditions[_SUBGROUP_SUBGROUP_GID] = $this->request->query['Subgroup_GId'];	
							
				
				if(isset($_REQUEST['Subgroup_Global']) && !empty($_REQUEST['Subgroup_Global']))
				$conditions[_SUBGROUP_SUBGROUP_GLOBAL] = $this->request->query['Subgroup_Global'];	
			
				if(isset($_REQUEST['Subgroup_Order']) && !empty($_REQUEST['Subgroup_Order']))
				$conditions[_SUBGROUP_SUBGROUP_ORDER] = $this->request->query['Subgroup_Order'];	

			    $fields = array();
				
				if(isset($_REQUEST['fields']) && !empty($_REQUEST['fields'])){
					$fields    = $_REQUEST['fields']; 
				}
                $SubgroupDetails   = $this->Subgroup->getDataByParamsSubgroup( $fields ,$conditions);
				
				
				if(isset($SubgroupDetails)&& count($SubgroupDetails)>0){
					
					$returnData['success'] = true;
					$returnData['data']  = $SubgroupDetails;
				}				
				else
				    $returnData['success'] = false;
					
				
			break;
			
			
			// Delete cases of Subgroup
			case 505:
				// service for deleting the Subgroup using  Subgroup name  value 
			if(isset($_REQUEST['Subgroup_Name']) && !empty($_REQUEST['Subgroup_Name'])){
			
				$Subgroup_Namevalue = $this->request->query['Subgroup_Name'];			
                $deleteBySubgroupName  = $this->Subgroup->deleteBySubgroupName($Subgroup_Namevalue);			   
			    if($deleteBySubgroupName>0){
					$returnData['message'] = 'Record deleted successfully';
					$returnData['success'] = true;	
					$returnData['returnvalue'] = $deleteBySubgroupName;
						
				}else{
					$returnData['success'] = false;
				}
			}else{
				
				$returnData['success'] = false;
				$returnData['message'] = 'Invalid request';	     //COM005; //'Invalid request'		
			}
			
            break;
			
			
				
			case 506:
				// service for deleting the Subgroup  id it can be one  or mutiple  
			//if(isset($_REQUEST['Subgroupids']) && !empty($_REQUEST['Subgroupids'])){
				$ids  = [423,424];			
                $deletebySubgroupIDS   = $this->Subgroup->deleteByIdsSubgroup($ids);
			    if($deletebySubgroupIDS >0){
					$returnData['message'] = 'Record deleted successfully';
					$returnData['success'] = true;		
					$returnData['returnvalue'] = $deletebySubgroupIDS;
						
				}else{
					$returnData['success'] = false;		
					
				}
			//}else{				
				
				//$returnData['success'] = false;
				//$returnData['message'] = 'Invalid request';	     //COM005; //'Invalid request'		
			//}
			
            break;			
			
			
				
			case 507:
				// service for deleting the Subgroup Name using  any parameters   
							
				$conditions = array();
			    
				if(isset($_REQUEST['Subgroup_Type']) && !empty($_REQUEST['Subgroup_Type']))
                $conditions[_SUBGROUP_SUBGROUP_TYPE] = trim($this->request->query['Subgroup_Type']);				
				
				if(isset($_REQUEST['Subgroup_GId']) && !empty($_REQUEST['Subgroup_GId']))
				$conditions[_SUBGROUP_SUBGROUP_GID] = trim($this->request->query['Subgroup_GId']);				
				
				if(isset($_REQUEST['Subgroup_Order']) && !empty($_REQUEST['Subgroup_Order']))
				$conditions[_SUBGROUP_SUBGROUP_ORDER] = trim($this->request->query['Subgroup_Order']);	
			
				if(isset($_REQUEST['Subgroup_Global']) && !empty($_REQUEST['Subgroup_Global']))
                $conditions[_SUBGROUP_SUBGROUP_GLOBAL] = trim($this->request->query['Subgroup_Global']);	
			
			    if(isset($_REQUEST['Subgroup_Name']) && !empty($_REQUEST['Subgroup_Name']))
                $conditions[_SUBGROUP_SUBGROUP_NAME] = trim($this->request->query['Subgroup_Name']);
			
			    if(isset($_REQUEST['Subgroup_NId']) && !empty($_REQUEST['Subgroup_NId']))
                $conditions[_SUBGROUP_SUBGROUP_NID] = trim($this->request->query['Subgroup_NId']);
			
                $deleteallSubgroup  = $this->Subgroup->deleteByParamsSubgroup($conditions);
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

						

            //default:
                
        
			endswitch;
			
			return $this->returnData($returnData, $convertJson);
	
        	//return $this->returnData($returnData, $convertJson);

		
		 }// service query ends here 
		 

		// - METHOD TO GET RETURN DATA
	public function returnData($data, $convertJson='_YES') {
		if($convertJson == '_YES') {
			$data = json_encode($data);
		}
		pr($data);
		die;

		//return $data;
		
	}
		
	

   } 
