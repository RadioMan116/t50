<?php
namespace Agregator\Common;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

abstract class AbstractExcel
{

	protected $spreadsheet;
	protected $sheet;

	function __construct(){
		$this->spreadsheet = new Spreadsheet();
		$this->sheet = $this->spreadsheet->getActiveSheet();
	}

	protected function getCoord(...$args){
		$column = ( is_numeric($args[0]) ? Coordinate::stringFromColumnIndex($args[0]) : $args[0] );
		$coord = $column . $args[1];
		if( isset($args[2]) && isset($args[3]) )
			$coord .= ":" . $this->getCoord($args[2], $args[3]);

		return $coord;
	}

	protected function saveFile($filePath){
		$filePath = str_replace(".xlsx", "", $filePath) . ".xlsx";
		$writer = new Xlsx($this->spreadsheet);
		$writer->save($filePath);
		if( file_exists($filePath) )
			return $filePath;

		return false;
	}

	protected function setColumnAutoSize(array $customSizeMap){
		foreach ($this->sheet->getColumnIterator() as $column)
    		$this->sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
	}

	protected function outFile(string $name, $clearHtml = true){
		if( $clearHtml )
			$GLOBALS["APPLICATION"]->RestartBuffer();

		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header("Content-Disposition: attachment;filename=\"{$name}.xlsx\"");
		header("Cache-Control: max-age=0");
		$writer = new Xlsx($this->spreadsheet);
		$writer->save('php://output');
		exit();
	}
}