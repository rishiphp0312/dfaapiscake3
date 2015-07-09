<?php
namespace DevInfoInterface\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;


/**
 * Area Component
 */
class AreaComponent extends Component
{
    
    // The other component your component uses
    public $components = [];
    public $AreaObj = NULL;
    public $AreaLevelObj = NULL;
 
	public function initialize(array $config)
    {
        //parent::initialize($config);
        $this->AreaObj = TableRegistry::get('DevInfoInterface.Areas');
        $this->AreaLevelObj = TableRegistry::get('DevInfoInterface.AreaLevel');
		        require_once(ROOT . DS . 'vendor' . DS . 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php');

    }
    

    /**
    * getDataByIds method
    *
    * @param array $conditions Conditions on which to search. {DEFAULT : empty}
    * @param array $fields Fields to fetch. {DEFAULT : empty}
    * @return void
    */
    public function getDataByIds($ids = null, $fields = [], $type = 'all' )
    {
		
        return $this->AreaObj->getDataByIds($ids, $fields, $type);
    }


    /**
     * getDataByParams method for Areas
     *
     * @param array $conditions Conditions on which to search. {DEFAULT : empty}
     * @param array $fields Fields to fetch. {DEFAULT : empty}
     * @return void
     */
    public function getDataByParams(array $fields, array $conditions, $type = 'all')
    {				        

        return $this->AreaObj->getDataByParams($fields, $conditions, $type);
    }
	
	
	public function exportArea($fields, $conditions){
		
		$module='Area';
		$authUserId = 1; //$this->Auth->User('id');   
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $startRow = $objPHPExcel->getActiveSheet()->getHighestRow();
        $rowCount = 1;
        $firstRow = ['A' => 'AreaId', 'B' => 'AreaName', 'C' => 'AreaLevel', 'D' => 'AreaGId', 'E' => 'ParentAreaId'];

        foreach ($firstRow as $index => $value) {
            $objPHPExcel->getActiveSheet()->SetCellValue($index . $rowCount, $value);
        }
		$conditions=['1'=>'1'];
		$areadData = $this->AreaObj->getDataByParams( $fields, $conditions,'all');
		
		$startRow = 2;
		foreach ($areadData as $index => $value) {
			 $value['Area_Parent_NId'];
			$newconditions =[];
			$newconditions =[_AREA_AREA_NID => $value['Area_Parent_NId']];
			$newfields =[_AREA_AREA_ID];
        	$parentnid= $this->getDataByParams($newfields,$newconditions);
			pr($parentnid);//die;
			if($value['Area_Parent_NId']!='-1')
			$parentnid= current($parentnid)[_AREA_AREA_ID];
			else
		    $parentnid= '-1';
			//die;
			$objPHPExcel->getActiveSheet()->SetCellValue('A' . $startRow, (isset($value['Area_ID'])) ? $value['Area_ID'] : '' );
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $startRow, (isset($value['Area_Name'])) ? $value['Area_Name'] : '');
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $startRow, (isset($value['Area_Level'])) ? $value['Area_Level'] : '');
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $startRow, (isset($value['Area_GId'])) ? $value['Area_GId'] : '' );
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $startRow, (isset($parentnid)) ? $parentnid : '' );
          //  $objPHPExcel->getActiveSheet()->SetCellValue('F' . $startRow,  '' );
          //  $objPHPExcel->getActiveSheet()->SetCellValue('G' . $startRow,  '' );
            $startRow++;
			
		}
	


        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $returnFilename = 'Export_'. $module . '_' . $authUserId . '_' . date('Y-m-d') . '.xlsx';
        $objWriter->save($returnFilename);
        return $returnFilename;
	}
	
	/**
     * getDataByParams for Area Level method
     *
     * @param array $conditions Conditions on which to search. {DEFAULT : empty}
     * @param array $fields Fields to fetch. {DEFAULT : empty}
     * @return void
    */
    public function getDataByParamsAreaLevel(array $fields, array $conditions, $type = 'all')
    {				        

        return $this->AreaLevelObj->getDataByParams($fields, $conditions, $type);
    }

    /**
     * deleteByIds method
     *
     * @param array $ids Fields to fetch. {DEFAULT : null}
     * @return void
     */
    public function deleteByIds($ids = null)
    {
        return $this->AreaObj->deleteByIds($ids);
    }

    /**
     * deleteByParams method for Areas 
     *
     * @param array $conditions Fields to fetch. {DEFAULT : empty}
     * @return void
     */
    public function deleteByParams($conditions = [])
    {	
        return $this->AreaObj->deleteByParams($conditions);
    }
	
	
	/**
     * deleteByParams method for Area Level 
     *
     * @param array $conditions Fields to fetch. {DEFAULT : empty}
     * @return void
     */
	
	
	public function deleteByParamsAreaLevel($conditions = [])
    {				        

        return $this->AreaLevelObj->deleteByParams($conditions);
    }

    /**
     * insertData method for Area
     *
     * @param array $fieldsArray Fields to insert with their Data. {DEFAULT : empty}
     * @return void
    */
    public function insertUpdateAreaData($fieldsArray = [])
    {
        return $this->AreaObj->insertData($fieldsArray);
    }
	
	
	/**
     * insertData method for Area level
     *
     * @param array $fieldsArray Fields to insert with their Data. {DEFAULT : empty}
     * @return void
    */
    public function insertUpdateAreaLevel($fieldsArray = [])
    {
        return $this->AreaLevelObj->insertData($fieldsArray);
    }


    
    
    /**
     * insertBulkData method
     *
     * @param array $insertDataArray Data to insert. {DEFAULT : empty}
     * @param array $insertDataKeys Columns to insert. {DEFAULT : empty}
     * @return void
     */
    public function insertBulkData($insertDataArray = [], $insertDataKeys = [])
    {
        return $this->AreaObj->insertBulkData($insertDataArray, $insertDataKeys);
    }
	
	
	 /**
     * insertBulkData method for Area level
     *
     * @param array $insertDataArray Data to insert. {DEFAULT : empty}
     * @param array $insertDataKeys Columns to insert. {DEFAULT : empty}
     * @return void
     */
    public function insertBulkDataAreaLevel($insertDataArray = [], $insertDataKeys = [])
    {
        return $this->AreaLevelObj->insertBulkData($insertDataArray, $insertDataKeys);
    }
	
	
	

    
    /**
     * insertOrUpdateBulkData method
     *
     * @param array $dataArray Fields to insert with their Data. {DEFAULT : empty}
     * @return void
     */
    public function insertOrUpdateBulkData($dataArray = [])
    {
        return $this->AreaObj->insertOrUpdateBulkData($dataArray);
    }
	
	
	/**
     * insertOrUpdateBulkData method for Area level
     *
     * @param array $dataArray Fields to insert with their Data. {DEFAULT : empty}
     * @return void
     */
    public function insertOrUpdateBulkDataAreaLevel($dataArray = [])
    {
        return $this->AreaLevelObj->insertOrUpdateBulkData($dataArray);
    }


    /**
     * updateDataByParams method
     *
     * @param array $fieldsArray Fields to insert with their Data. {DEFAULT : empty}
     * @return void
     */
    public function updateDataByParams($fieldsArray = [], $conditions = [])
    {
        return $this->AreaObj->updateDataByParams($fieldsArray, $conditions);
    }
	
	 /**
     * updateDataByParams method for Area level
     *
     * @param array $fieldsArray Fields to insert with their Data. {DEFAULT : empty}
     * @return void
     */
    public function updateDataByParamsAreaLevel($fieldsArray = [], $conditions = [])
    {
        return $this->AreaLevelObj->updateDataByParams($fieldsArray, $conditions);
    }
	
	
	
	/**
    * updateDataByParams method for  Area
    *
    * @param array $fieldsArray Fields to insert with their Data. {DEFAULT : empty}
    * @return void
    */
    public function updateDataByParamsArea($fieldsArray = [], $conditions = [])
    {
        return $this->AreaObj->updateDataByParams($fieldsArray, $conditions);
    }


}
