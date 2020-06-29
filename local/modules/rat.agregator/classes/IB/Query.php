<?php
namespace Agregator\IB;

use \Bitrix\Main\Loader;

abstract class Query extends \Agregator\Cache
{
	protected $sort;
	protected $filter;
	protected $select;
	protected $props;
	protected $limit;
	protected $navData;
	protected $index;
	protected $column;

	function __construct($iblockCode){
		parent::__construct();
		Loader::IncludeModule("iblock");
		$iblockCode = htmlspecialchars($iblockCode);
		$iblockId = $this->getIblockId($iblockCode);
		$this->sort = array();
		$this->filter = array(
			"ACTIVE" => "Y",
			"IBLOCK_ID" => $iblockId,
		);
		$this->select = array("ID", "IBLOCK_ID");
		$this->props = array();
		$this->limit = false;
	}

	function sort(array $sortArr){
		$this->sort = $sortArr;
		return $this;
	}

	function filter(array $filter){
		foreach($filter as $code => $value)
			$this->filter[$code] = $value;

		return $this;
	}

	function props(){
		$props = $this->getArgArray(func_get_args());
		foreach($props as $prop)
			$this->props[] = "PROPERTY_{$prop}";

		return $this;
	}

	function select(){
		$select = $this->getArgArray(func_get_args());
		$this->select = array_merge($this->select, $select);
		$this->select = array_unique($this->select);
		return $this;
	}

	private function getArgArray($args){
		if( is_array($args[0]) && count($args) == 1 )
			return $args[0];

		return $args;
	}

	function limit($limit){
		$arNavStartParams = false;
		if( is_array($limit) && count($limit) == 1 ){
			$arNavStartParams = array(
				"iNumPage" => intval(key($limit)),
				"nPageSize" => intval(current($limit)),
			);
		} elseif( ($limit = intval($limit)) > 0 ) {
			$arNavStartParams = array( "nTopCount" => $limit );
		}
		$this->limit = $arNavStartParams;

		return $this;
	}

	function setIndex($index){
		$this->index = $index;
		return $this;
	}

	function setColumn($column){
		$this->column = $column;
		return $this;
	}

	function setNav($pageSize = 10, $nPageWindow = 5, $navTitle = ""){
		$this->navData = new \StdClass;
		$this->navData->pageSize = $pageSize;
		$this->navData->nPageWindow = $nPageWindow;
		$this->navData->title = $navTitle;
		$this->navData->navPrint = "";
		return $this;
	}

	function getNavPrint(){
		if( $this->navData == null || $this->navData->navPrint == null )
			return "";
		return $this->navData->navPrint;
	}

	function get(){
		$res = \CIBlockElement::getList($this->sort, $this->filter, false, $this->limit, $this->fullSelect());
		if( $this->navData != null ){
			$res->NavStart($this->navData->pageSize);
			$res->nPageWindow = $this->navData->nPageWindow;
			$this->navData->navPrint = $res->GetPageNavString($this->navData->title);
		}
		return $res;
	}

	private function fullSelect(){
		return array_merge($this->select, $this->props);
	}

	protected function getQueryHash(){
		$hash = array();
		if( !empty($this->sort) )
			$hash[] = implode("", $this->sort);
		foreach($this->filter as $code => $item)
			$hash[] = $code . $item;
		$select = $this->fullSelect();
		if( !empty($select) )
			$hash[] = implode("", $select);
		if( !empty($this->limit) )
			$hash[] = ( is_array($this->limit) ? current($this->limit) : $this->limit ) ;

		$hash[] = $this->index;
		$hash[] = $this->column;

		$hash = implode("", $hash);
		$hash = substr(md5($hash), 0, 7);
		return $hash;
	}

	private function getIblockId($code){
		static $objIBlocks;
		if( $objIBlocks == null )
			$objIBlocks = new IBlocks;

		return $objIBlocks->getIblockId($code);
	}
}