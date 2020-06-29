<?php
namespace Agregator\Product\Calculator;

use rat\agregator\Formula;
use T50GlobCache;
use Agregator\Logger;

abstract class FormulaData extends Logger
{
	const MODE_FREE = "free";
	const MODE_RRC = "rrc";
	protected $id;
	protected $modeMapIdCode = array();
	protected $modeMap = array();
	public static $mapJsCodeToDbCodeValidator = array(
		"percent" => ["UF_PERCENT", "doubleVal"],
		"min_purchase" => ["UF_MIN_PURCHASE", "intVal"],
		"max_purchase" => ["UF_MAX_PURCHASE", "intVal"],
		"min_commission" => ["UF_MIN_COMMISSION", "intVal"],
		"max_commission" => ["UF_MAX_COMMISSION", "intVal"],
	);

	function __construct(){
		parent::__construct("formula");
		$this->modeMap = \T50GlobVars::get("HLPROPS")[Formula::getTableName()]["UF_MODE"];
		foreach($this->modeMap as $code => $item)
			$this->modeMapIdCode[$item["id"]] = $code;
	}

	function loadData(int $id, $separateMainChilds = false){
		if( $id <= 0 )
			return;

		$filter = array('LOGIC' => 'OR', ['ID' => $id], ['UF_PARENT_ID' => $id]);
		$arResult = array();
		$result = Formula::clas()::getList(["filter" => $filter]);
		while( $row = $result->fetch() ){
			$row["UF_MODE"] = $this->modeMapIdCode[$row["UF_MODE"]];
			$arResult[] = $row;
		}

		if( $separateMainChilds ){
			$main = null;
			$childs = [];
			foreach($arResult as $item){
			    if( $item["ID"] == $id ){
			    	$main = $item;
			    } else {
			    	$childs[] = $item;
			    }
			}
			return [$main, $childs];
		}

		return $arResult;
	}

	function getId(){
		return $this->id;
	}

}