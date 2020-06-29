<?php
namespace Agregator\Common;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportExcel
{
	private $spreadsheet;
	private $ext = "xlsx";
	private $root;

	private $listIndex = -1;

	function __construct(){
		$this->spreadsheet = new Spreadsheet();
	}

	function getObject(){
		return $this->spreadsheet;
	}

	function createList($listName, $data, $columns = array()){
		$data = array_values($data);
		$this->listIndex ++;

		$sheet = $this->spreadsheet->createSheet($this->listIndex);
		$sheet->setTitle($listName);

		$rowAdd = 1;
		if( !empty($columns) ){
			foreach($columns as $columnIndex => $value)
				$sheet->setCellValueByColumnAndRow(($columnIndex + 1), 1, $value);
			$rowAdd = 2;
		}

		foreach($data as $rowIndex => $row){
			$rowNum = $rowIndex + $rowAdd;
			if( !is_array($row) ){
				$sheet->setCellValueByColumnAndRow(1, $rowNum, $row);
			} else {
				$row = array_values($row);
				foreach($row as $columnIndex => $value)
					$sheet->setCellValueByColumnAndRow(($columnIndex + 1), $rowNum, $value);
			}
		}

		return $this;
	}

	private function finalize(){
		// setAutoSize
		for($i = $this->listIndex; $i >= 0; $i--){
			$sheet = $this->spreadsheet->setActiveSheetIndex($i);
			$lastColumn = $sheet->getHighestColumn();
			$range = range("A", $lastColumn);
			if( empty($range) || count($range) > 26 )
				continue;

			foreach($range as $columnId){
				$sheet->getColumnDimension($columnId)->setAutoSize(true);
			}
		}
	}

	function setRoot($dir){
		$this->root = $dir;
		return $this;
	}

	private function validatePath($filePath){
		$ext = pathinfo($filePath, PATHINFO_EXTENSION);
		if( empty($ext) )
			$filePath .= ".{$this->ext}";
		else
			$filePath = str_replace(".{$ext}", ".{$this->ext}", $filePath);

		if( !empty($this->root) ){
			$filePath = ltrim($filePath);
			$filePath = $this->root . "/" . $filePath;
		}

		return $filePath;
	}

	function isEmpty(){
		return ( $this->listIndex == -1 );
	}

	function saveFile($filePath){
		$this->finalize();
		$filePath = $this->validatePath($filePath);

		$writer = new Xlsx($this->spreadsheet);
		$writer->save($filePath);
		if( file_exists($filePath) )
			return $filePath;

		return false;
	}

	function outFile($fileName){
		$this->finalize();
		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header("Content-Disposition: attachment;filename=\"{$fileName}.{$this->ext}\"");
		header("Cache-Control: max-age=0");

		$writer = new Xlsx($this->spreadsheet);
		$writer->save('php://output');
	}
}