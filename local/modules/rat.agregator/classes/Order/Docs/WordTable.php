<?php

namespace Agregator\Order\Docs;

use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\Shared\Converter;
use \PhpOffice\PhpWord\SimpleType\Jc;

class WordTable
{
	private $columnsWidthPercent;
	private $width;
	private $data;

	function setColumnsWidthPercent(array $columnsWidthPercent){
		$this->columnsWidthPercent = $columnsWidthPercent;
		return $this;
	}

	function setWidth(int $width){
		if( $width > 0 )
			$this->width = Converter::cmToTwip($width);
		return $this;
	}

	function setData(array $data){
		$count = 0;
		foreach($data as $k => $item){
		    if( $count == 0 ){
		    	$count = count($item);
		    } elseif( count($item) != $count ) {
		    	throw new \InvalidArgumentException("invalid count in line {$k}");
		    }
		}
		$this->data = $data;
		return $this;
	}

	private function calcColumnsWidth($commonWidth, $columnsCount){
		$percents = $this->columnsWidthPercent ?? [];
		if( empty($percents) || array_sum($percents) > 100 || count($percents) != $columnsCount )
			$percents = array_fill(0, $columnsCount, 100 / $columnsCount);

		$percents = array_map(function ($percent) use($commonWidth){
			return $percent / 100 * $commonWidth;
		}, $percents);

		return $percents;
	}

	function build(){
		$data = $this->data;
		if( !isset($data) )
			throw new \RuntimeException("data not initialized");

		$columnsCount = count($data[0]);
		$width = $this->width ?? Converter::cmToTwip(18);
		$columnsWidth = $this->calcColumnsWidth($width, $columnsCount);

		$table = new Table([
			'borderSize' => 1,
			'cellMargin' => 100,
			'width' => $width,
			'unit' => TblWidth::TWIP
		]);
		foreach($data as $row){
			$table->addRow();
		    foreach($row as $k => $value){
		    	$alignment = (is_numeric(str_replace(" ", "", $value)) ? Jc::RIGHT: Jc::LEFT);
				$table->addCell($columnsWidth[$k])
					->addTextRun(['alignment' => $alignment, "hanging" => 0])
					->addText($value);
		    }
		}

		return $table;
	}
}
