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
    //Loading Componenets
    public $components = ['Indicator', 'Unit', 'Timeperiod','Subgroup'];

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
                
                $fields = ['Indicator_Name', 'Indicator_Info'];
                $conditions = ['Indicator_GId IN'=>['POPDEN', 'AREA']];
                
                //getDataByParams(array $fields, array $conditions)
                $returnData = $this->Indicator->getDataByParams($fields, $conditions); 
                break;

            case 103: //Delete Data using Indicator_NId -- Indicator table
                
                //deleteByIds($ids = null)
                $returnData = $this->Indicator->deleteByIds([1,2]); 
                break;

            case 104: //Delete Data using Conditions -- Indicator table
                
                $conditions = ['Indicator_GId IN'=>['TEST_GID', 'TEST_GID2']];

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

            case 107: //Select Data using Unit_NId -- Unit table

                //getDataByIds($ids = null, $fields = [], $type = 'all' )
                $returnData = $this->Unit->getDataByIds([10,41]); 
                break;

            case 108: //Select Data using Conditions -- Unit table
                
                $fields = ['Unit_Name', 'Unit_Global'];
                $conditions = ['Unit_GId IN'=>['POPDEN', 'AREA']];

                //getDataByParams(array $fields, array $conditions)
                $returnData = $this->Unit->getDataByParams($fields, $conditions); 
                break;

            case 109: //Delete Data using Unit_NId -- Unit table
                
                //deleteByIds($ids = null)
                $returnData = $this->Unit->deleteByIds([42]); 
                break;

            case 110: //Delete Data using Conditions -- Unit table
                
                $conditions = ['Unit_GId IN'=>['SOME_001_TEST', 'SOME_003_TEST']];

                //deleteByParams(array $conditions)
                $returnData = $this->Unit->deleteByParams($conditions);

            case 111: //Insert New Data -- Unit table
                
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

            case 112: //Update Data using Conditions -- Unit table
                
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

            case 113: //Bulk Insert Data -- Unit table
                
                //The following line should do the same like App::import() in the older version of cakePHP
                require_once(ROOT . DS . 'vendor' . DS  . 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php');

                $filename = 'C:\-- Projects --\Bulk_unit_test_file.xlsx';
                $insertFieldsArr = [];
                $insertDataArr = [];
                $dataArr = [];

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
                            //echo $val . '(Type ' . $dataType . ')<br>';
                            
                            if($row == 1){
                                $insertFieldsArr[] = $val;
                            }else{
                                $insertDataArr[$row][$insertFieldsArr[$col]] = $val;
                            }
                        }

                    }
                }
                
                $preparedData['Unit'] = array_values($insertDataArr);
                
                echo '<pre>'; print_r($preparedData); exit;

                if($this->request->is('post')):
                    //insertData(array $fieldsArray = $this->request->data)
                    $returnData = $this->Unit->insertBulkData($preparedData);
                endif;

                break;

           
			// nos starting with 301 are for timeperiod
				
			case 309:
				// service for getting the Timeperiod details on basis of ids
 				// can be one or multiple in form of array // passing  $ids, array $fields ,$type default is all
			    $ids     = array(2,3);
			    $fields  = array();
			    $fields  = array('TimePeriod_NId','TimePeriod'); // fields can be blank also 
			    $type    = 'all'; // type can be list or all only 
                $getDataByTimeperiod  = $this->Timeperiod->getDataByIds($ids,$fields,$type);
			  
                break;	
				
			case 310:
				// service for getting the Timeperiod details on basis of Timeperiod 
				// passing  $TimePeriodvalue,  $periodicity
			if(isset($_REQUEST['TimePeriodData']) && !empty($_REQUEST['TimePeriodData'])){
			
				$TimePeriodvalue = $this->request->query['TimePeriodData'];			
				$Periodicityvalue = $this->request->query['periodicity'];			
                $getDataByTimeperiod  = $this->Timeperiod->getDataByTimeperiod($TimePeriodvalue,$Periodicityvalue);
			    pr($getDataByTimeperiod);
				die;
			}
			    break;	
			
			case 311:
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
			   
			}
            
			    break;
			
			case 312:
				// service for deleteing the timeperiod using  timeperiod value 
			if(isset($_REQUEST['TimePeriodData']) && !empty($_REQUEST['TimePeriodData'])){
			
				$TimePeriodvalue = $this->request->query['TimePeriodData'];			
                $deleteByTimeperiod  = $this->Timeperiod->deleteByTimePeriod($TimePeriodvalue);
			    pr($deleteByTimeperiod);
			}
                break;
				
			case 313:
				// service for deleteing the timeperiod using  id it can be one  or mutiple  
			if(isset($_REQUEST['TimePeriodids']) && !empty($_REQUEST['TimePeriodids'])){
			    //$TimePeriodids = $this->request->query['TimePeriodids'];			
				$ids  = [7,8];			
                $deleteallTimeperiodIDS   = $this->Timeperiod->deleteByIds($ids);
			    pr($deleteallTimeperiodIDS);
			}
                break;	
				
			case 314:
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
			    pr($deleteallTimeperiod);
			}
                break;	
           case 315:
				// service for saving  details of timeperiod 
			if(isset($_REQUEST['TimePeriodData']) && !empty($_REQUEST['TimePeriodData'])){
			
				//$TimePeriodvalue = $this->request->query['TimePeriodData'];
				 $data = array();
				 //$data = $this->request->data();
				 $data['TimePeriod']  = $_REQUEST['TimePeriodData'];
				 $data['Periodicity'] = $_REQUEST['Periodicity']='A';
				
               $getDataByTimeperiod  = $this->Timeperiod->insertData($data);
			   pr($getDataByTimeperiod);
			   //die;
			}
                break;
				
			case 401:
				// service for saving  subgroup type name 
			if(isset($_REQUEST['subgrouptypename']) && !empty($_REQUEST['subgrouptypename'])){
			
				$subgrouptypename = $this->request->query['subgrouptypename'];
			
               $savedataforUTSubgroupTypeEn  = $this->Subgroup->savesingleSubgroupTypeName($subgrouptypename);
			   pr($savedataforUTSubgroupTypeEn);die;
			}

                break;

            default:
                
        
        endswitch;

        echo '<pre>'; print_r($returnData);exit;
        return $returnData;

    }
}
