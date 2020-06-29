<?php
namespace Agregator\Order;

use Agregator\IB\IBlocks;
use Agregator\IB\Elements;
use Agregator\IB\Section;
use CIBlockSection;
use T50GlobVars;
use CIBlock;

class InstallationPrices
{
	private $providerCode;
	private $categories = [];
	private $categoryId;

	function getProviders(){
		$res = CIBlock::getList(array("NAME" => "ASC"), array("=TYPE" => "installation"));
		$data = [];
		while( $result = $res->Fetch() )
			$data[$result["CODE"]] = $result["NAME"];

		return $data;
	}

	function setProvider($code){
		$this->providerCode = null;
		$this->categories = [];
		$this->categoryId = null;
		$providerId = (new IBlocks())->getIblockId($code);
		if( $providerId > 0 ){
			$this->providerCode = $code;
			$this->categories = Section::getList($code);
		}

		return $this;
	}

	function getProvider(){
		return $this->providerCode;
	}

	function setCategory(int $categoryId){
		$this->categoryId = null;
		if( isset($this->categories[$categoryId]) )
			$this->categoryId = $categoryId;

		return $this;
	}

	function getCategoies(){
		return $this->categories;
	}

	function getServices(){
		if( !isset($this->categoryId) || !isset($this->providerCode) )
			return [];

		$services = (new Elements($this->providerCode))
			->sort(["NAME" => "ASC"])
			->filter(["SECTION_ID" => $this->categoryId])
			->setIndex("ID")
			->select("NAME", "ID", "IBLOCK_SECTION_ID")->props("SALE", "PURCHASE", "REMCITY_ID", "PRICE")
			->getListFetch();

		return $services;
	}

	function getServiceById(int $id ){
		if( !isset($this->providerCode) )
			return null;

		$service = (new Elements($this->providerCode))
			->select("NAME", "ID", "IBLOCK_SECTION_ID")->props("SALE", "PURCHASE", "REMCITY_ID", "PRICE")
			->getOneFetchById($id);

		if( empty($service) )
			return null;

		return $service;
	}

}
