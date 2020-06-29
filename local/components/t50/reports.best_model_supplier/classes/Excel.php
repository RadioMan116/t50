<?php

namespace ReportsBestModelSupplierComponent;

use Agregator\Common\AbstractExcel;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class Excel extends AbstractExcel
{
	private $arResult;
	private $mapHeader = [
		"ID" => "unid",
		"UF_TITLE" => "Название",
		"UF_MODEL_PRINT" => "Модель",
		"CATEGORY" => "Раздел",
		"BRAND" => "Бренд",
		"SHOP" => "Магазин",
		"FORMULA" => "Формула",
		"SALE" => "Продажная цена",
		"PURCHES" => "Закупочная цена",
		"BEST_COMMISSION" => "Комиссия",
	];

	function setArResult(array $arResult){
		$this->arResult = $arResult;
		return $this;
	}

	function export(){
		$this->writeHeader();
		$this->writeData();

		$column = count($this->mapHeader);
		$highestRow = $this->sheet->getHighestRow();
		$this->sheet->duplicateStyle(
			$this->getStyle("BORDER_RIGHT"),
			$this->getCoord($column, 1, $column, $highestRow)
		);
		$this->outFile("отчет");
	}

	private function writeHeader(){
		$column = 0;
		foreach($this->mapHeader as $title)
			$this->sheet->setCellValueByColumnAndRow(++$column, 1, $title);

		foreach($this->arResult["USED_SUPPLIERS"] as $supplierId){
			$supplierName = $this->arResult["SUPPLIERS"][$supplierId];
			$this->sheet->setCellValueByColumnAndRow(++$column, 1, $supplierName);
			if( $this->arResult["SHOW_COND_AND_SALE"] ){
				$this->sheet->setCellValueByColumnAndRow(++$column, 1, $supplierName . " (продажная)");
				$this->sheet->setCellValueByColumnAndRow(++$column, 1, $supplierName . " (условие)");
			}
		}
	}

	private function writeData(){
		foreach($this->arResult["ITEMS"] as $index => $item){
			$rowNum = $index + 2;
			$this->writeDataRow($rowNum, $item);
		}
	}

	private function writeDataRow(int $numRow, array $data){
		$column = 0;
		foreach($this->mapHeader as $code => $title)
			$this->sheet->setCellValueByColumnAndRow(++$column, $numRow, $data[$code]);

		foreach($this->arResult["USED_SUPPLIERS"] as $supplierId){
			$supplier = $data["SUPPLIERS"][$supplierId];

			$purchesColumn = ++$column;
			$this->sheet->setCellValueByColumnAndRow($purchesColumn, $numRow, $supplier["purches"]);
			if( $supplier["is_best"] ){
				$this->sheet->duplicateStyle(
					$this->getStyle("GREEN_FONT"),
					$this->getCoord($purchesColumn, $numRow)
				);
			}

			if( $this->arResult["SHOW_COND_AND_SALE"] ){
				$this->sheet->setCellValueByColumnAndRow(++$column, $numRow, $supplier["sale"]);
				$this->sheet->setCellValueByColumnAndRow(++$column, $numRow, $supplier["cond"]);
			}
		}
	}

	private function getStyle($code){
		static $styles;
		if( isset($styles) )
			return $styles[$code];

		$styles = [
			"GREEN_FONT" => (new Style())->applyFromArray([
	    		'fill' => ['color' => ['rgb' => "8AFAFF"], 'fillType' => Fill::FILL_SOLID]
	    	]),

	    	"BORDER_RIGHT" => (new Style())->applyFromArray([
				'borders' => ['right' => ['borderStyle' => Border::BORDER_MEDIUM]]
			]),
		];

    	return $styles[$code];
	}
}