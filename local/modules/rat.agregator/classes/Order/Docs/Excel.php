<?php

namespace Agregator\Order\Docs;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;


class Excel extends Generator
{
	private $sheet;
	private $extension;
	private $coordCols;
	private $imagesDir;

	function __construct(){
		$this->coordCols = $this->getColSims();
	}

	function init(string $path){
		$pathInfo = pathinfo($path);
		$this->extension = ucfirst(strtolower($pathInfo["extension"]));
		$this->handler = IOFactory::load($path);
		$this->sheet = $this->handler->getActiveSheet();
		$this->imagesDir = $pathInfo["dirname"] . "/images/";
	}

	function setValues(array $arCoordValue){
		foreach($arCoordValue as $coord => $value){
		    $this->sheet->setCellValue($coord, $value);
		}
		return $this;
	}

	function setImage($coord, $imageName, array $options = array()){
		$drawing = new Drawing();
		$drawing->setPath($this->imagesDir . $imageName);
		$drawing->setCoordinates($coord);
		if( is_int($options["X"]) )
			$drawing->setOffsetX($options["X"]);
		if( is_int($options["Y"]) )
			$drawing->setOffsetY($options["Y"]);
		if( is_int($options["H"]) )
			$drawing->setHeight($options["H"]);
		$drawing->setWorksheet($this->sheet);
		return $this;
	}

	function fillTable(int $rowStart, $rowEnd, array $cellSimCoords, array $data){
		$countRows = $rowEnd - $rowStart + 1;
		$countData = count($data);
		$this->repeatRow($rowStart, $countData - $countRows);
		foreach($data as $row){
		    foreach($row as $k => $value){
		    	$coord = $cellSimCoords[$k] . $rowStart;
		        $this->sheet->setCellValue($coord, $value);
		    }
		    $rowStart ++;
		}

		return $this;
	}

	function download(string $name){
		$GLOBALS['APPLICATION']->RestartBuffer();
		$writer = IOFactory::createWriter($this->handler, $this->extension);
    	header("Content-Disposition: attachment; filename=" . $name);
		header("Pragma: no-cache");
		header("Expires: 0");
    	$writer->save("php://output");
    	die();
	}

	private function getColSims(){
		$arResult = [];
		foreach(["", "A", "B"] as $sim1){
		    foreach(range("A", "Z") as $sim2){
		    	$arResult[] = $sim1 . $sim2;
		    }
		}
		return $arResult;
	}

	private function repeatRow(int $row, int $count){
		if( $count <= 0 )
			return;

		$copyMerges = array_filter($this->sheet->getMergeCells(), function($coord) use($row) {
			return substr_count($coord, $row);
		});

		$this->sheet->insertNewRowBefore($row + 1, $count);

		for($i = 1; $i <= $count; $i++) {
			$rowForMerge = $row + $i;
			foreach($copyMerges as $copyMerge){
				$mergeCell = str_replace($row, $rowForMerge, $copyMerge);
				$this->sheet->mergeCells($mergeCell);
			}
		}
	}
}