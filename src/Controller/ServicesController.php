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
			
			
				
			case 301:
				// service for getting the Timeperiod details on basis of ids
 				// can be one or multiple in form of array // passing  $ids, array $fields ,$type default is all
			    if($this->request->is('post')){
			
					$ids     = array(2,3);
					$fields  = array();
					$fields  = array('TimePeriod_NId','TimePeriod'); // fields can be blank also 
					$type    = 'list'; // type can be list or all only 
					$getDataByTimeperiod  = $this->Timeperiod->getDataByIds($ids,$fields,$type);
					$returnData['data'] = $getDataByTimeperiod;
					$returnData['success'] = true;	
					
				
				}else{
					$returnData[] = false;
					$returnData['success'] = false;
					$returnData['message'] = 'Invalid request';	     //COM005; //'Invalid request'		
				}  
            
			break;	
			
			
				
			case 302:
			//  service for getting the Timeperiod details on basis of Timeperiod //by rishi 
			//  passing  $TimePeriodvalue,  $periodicity is optional
			if(isset($_REQUEST['TimePeriodData']) && !empty($_REQUEST['TimePeriodData'])){
				
				$TimePeriodvalue       = $this->request->query['TimePeriodData'];			
				$Periodicityvalue      = $this->request->query['periodicity'];			
                $getDataByTimeperiod   = $this->Timeperiod->getDataByTimeperiod($TimePeriodvalue,$Periodicityvalue);
			    $returnData['data']    = $getDataByTimeperiod;
				$returnData['success'] = true;	
				
			}else{				
				$returnData[] = false;
				$returnData['success'] = false;
				$returnData['message'] = 'Invalid request';	     //COM005; //'Invalid request'		
			}  
			break;

			
			
			case 303:
				// service for getting the Timeperiod details on basis of any parameter  
				// passing array $fields, array $conditions
			
			if(isset($_REQUEST['TimePeriod']) && !empty($_REQUEST['TimePeriod'])){
				
				$conditions = array();
			    
				if(isset($_REQUEST['TimePeriod']) && !empty($_REQUEST['TimePeriod']))
                $conditions['TimePeriod'] = $this->request->query['TimePeriod'];	
					
    			if(isset($_REQUEST['periodicity']) && !empty($_REQUEST['periodicity']))
				$conditions['periodicity'] = $this->request->query['periodicity'];	
			
				if(isset($_REQUEST['StartDate']) && !empty($_REQUEST['StartDate']))
                $conditions['StartDate'] = $this->request->query['StartDate'];	
			
			    if(isset($_REQUEST['TimePeriod_NId']) && !empty($_REQUEST['TimePeriod_NId']))
                $conditions['TimePeriod_NId'] = $this->request->query['TimePeriod_NId'];	

			    $fields = array();
				
                $getDataByTimeperiod  = $this->Timeperiod->getDataByParams( $fields ,$conditions);
				$returnData['data'] = $getDataByTimeperiod;
				$returnData['success'] = true;					
			   
			}else{
				
				$returnData[] = false;
				$returnData['success'] = false;
				$returnData['message'] = 'Invalid request';	     //COM005; //'Invalid request'		
			}
            
			break;
			
			
			
			case 304:
				// service for deleteing the timeperiod using  timeperiod value 
			if(isset($_REQUEST['TimePeriodData']) && !empty($_REQUEST['TimePeriodData'])){
			
				$TimePeriodvalue = $this->request->query['TimePeriodData'];			
                $deleteByTimeperiod  = $this->Timeperiod->deleteByTimePeriod($TimePeriodvalue);			   
			    $returnData['message'] = 'Record deleted successfully';
				$returnData['success'] = true;	
				$returnData['returnvalue'] = $deleteByTimeperiod;
				
			}else{
				
				$returnData[] = false;
				$returnData['success'] = false;
				$returnData['message'] = 'Invalid request';	     //COM005; //'Invalid request'		
			}
			
            break;
			
			
				
			case 305:
				// service for deleteing the timeperiod using  id it can be one  or mutiple  
			if(isset($_REQUEST['TimePeriodids']) && !empty($_REQUEST['TimePeriodids'])){
			    //$TimePeriodids = $this->request->query['TimePeriodids'];			
				$ids  = [7,8];			
                $deleteallTimeperiodIDS   = $this->Timeperiod->deleteByIds($ids);
			    $returnData['message'] = 'Record deleted successfully';
				$returnData['success'] = true;		
				$returnData['returnvalue'] = $deleteallTimeperiodIDS;
				
			}else{				
				
				$returnData[] = false;
				$returnData['success'] = false;
				$returnData['message'] = 'Invalid request';	     //COM005; //'Invalid request'		
			}
			
            break;			
			
			
				
			case 306:
				// service for deleteing the timeperiod using  any parameters   
			if(isset($_REQUEST['TimePeriod']) && !empty($_REQUEST['TimePeriod'])){
			    //$TimePeriodids = $this->request->query['TimePeriodids'];			
				$conditions = array();
			    
				if(isset($_REQUEST['TimePeriod']) && !empty($_REQUEST['TimePeriod']))
                $conditions['TimePeriod'] = $this->request->query['TimePeriod'];	
				
				if(isset($_REQUEST['periodicity']) && !empty($_REQUEST['periodicity']))
				$conditions['periodicity'] = $this->request->query['periodicity'];	
			
				if(isset($_REQUEST['StartDate']) && !empty($_REQUEST['StartDate']))
                $conditions['StartDate'] = $this->request->query['StartDate'];	
			
			    if(isset($_REQUEST['TimePeriod_NId']) && !empty($_REQUEST['TimePeriod_NId']))
                $conditions['TimePeriod_NId'] = $this->request->query['TimePeriod_NId'];
			
                $deleteallTimeperiod  = $this->Timeperiod->deleteByParams($conditions);
				$returnData['message'] = 'Records deleted successfully';
				$returnData['success'] = true;		
				$returnData['returnvalue'] = $deleteallTimeperiod;
				
			}else{
				
				$returnData[] = false;
				$returnData['success'] = false;
				$returnData['message'] = 'Invalid request';	     //COM005; //'Invalid request'		
			}	
			break;	
			
			
			
            case 307: 
			// service for saving  details of timeperiod 
			if($this->request->is('post')){				 
				$data = array();
				$data['TimePeriod']   = $_REQUEST['TimePeriodData']=2091;
				$data['Periodicity']  = $_REQUEST['Periodicity']='C';
				// $data['TimePeriod_NId']  = $_REQUEST['TimePeriod_NId']=300;
			    $this->request->data  = $data;
                $getDataByTimeperiod  = $this->Timeperiod->insertDataTimeperiod($this->request->data);
			  	$returnData['success'] = true;		
				$returnData['message'] = 'Record inserted successfully!!';
				$returnData['returnvalue'] = $getDataByTimeperiod;
				
			}else{	
			
				$returnData[] = false;
				$returnData['success'] = false;
				$returnData['message'] = 'Invalid request';	     //COM005; //'Invalid request'		
			}	
			break;
			
			
				
			case 350:
			// service built for testing of timeperiod format
			//$timeperiodvalue=$_REQUEST['TimePeriodData']='2013.06-2015.08';			
			// $data = $this->checkTimePeriodFormat($timeperiodvalue);
	        
			// pr($data);
			//die;			
			break;

			
			// service no. starting with 401 are for subgroup type 
			case 401:
			// service for saving  subgroup type name 
			//if(isset($_REQUEST['subgrouptypename']) && !empty($_REQUEST['subgrouptypename'])){
			 // Subgroup_Type_NId is auto increment 
			 $data = array();
			 $data['Subgroup_Type_Name']   = $_REQUEST['Subgroup_Type_Name']= 'animals112';
			 $data['Subgroup_Type_GID']    = $_REQUEST['Subgroup_Type_GID']=$this->Common->guid();
			 $this->request->data          = $data;
             $saveDataforSubgroupType = $this->Subgroup->insertDataSubgroupType($this->request->data);
			 pr($saveDataforSubgroupType);
			
			//}

            break;
			
			
			// service no. starting from  501 are for subgroup
			
			case 501:
			// service for saving  subgroup  name 
			if(isset($_REQUEST['subgrouptypename']) && !empty($_REQUEST['subgrouptypename'])){
			 // Subgroup_NId is auto increment 
			 $data = array();
			 $data['Subgroup_Name']   = $_REQUEST['Subgroup_Name'] = 'cat';
			 $data['Subgroup_Type']   = $_REQUEST['Subgroup_Type'] =  213;
			 $data['Subgroup_GId']    = $_REQUEST['Subgroup_GId']  =  $this->Common->guid();
			 $this->request->data     = $data;
             $saveDataforSubgroupType = $this->Subgroup->insertDataSubgroup($this->request->data);
			 pr($saveDataforSubgroupType);
			 //die;
			}

            break;
			
		
			case 502:
			
            // service for saving bulk upload data  for subgroup
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
pr($data);		
			
			die('hua');	
			
			
				
			break;
			

            default:
                
        
        endswitch;

        echo '<pre>'; print_r($returnData);exit;
        return $returnData;
		
		 }// service query ends here 
		 

		// - METHOD TO GET RETURN DATA
	public function returnData($data, $convertJson='_YES') {
		
		$data['IsLoggedIn'] = false;
		if ($this->Auth->user()) {
			$data['IsLoggedIn'] = true;
		}
		if($convertJson == '_YES') {
			$data = json_encode($data);
		}

		return $data;
		
	}
		
	

   } 
