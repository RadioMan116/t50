<?php
namespace Agregator\Product\Calculator;

use rat\agregator\Formula;
use T50GlobVars;
use T50DB;

class FormulaUpdate extends FormulaData
{
	private $data = [
		"UF_MODE" => self::MODE_FREE,
		"UF_USE_SUPPLIERS_RRC" => 0,
		"UF_CHECK_RRC" => 1,
		"UF_SUPPLIERS_RRC" => [],
		"UF_TITLE" => "",
		"UF_COMMENT" => "",
	];

	private $parameters = [];

	function setId(int $id){
		if( $id > 0 )
			$this->id = $id;

		return $this;
	}

	function setPriceType($type){
		if( in_array($type, [self::MODE_FREE, self::MODE_RRC]) )
			$this->data["UF_MODE"] = $type;

		return $this;
	}

	function setModeCalcFromSalePrice($mode){
		$mode = ( (bool) $mode ? 1 : 0 );
		$this->data["UF_USE_SUPPLIERS_RRC"] = $mode;
		return $this;
	}

	function setModeCheckRrc($mode){
		$mode = ( (bool) $mode ? 1 : 0 );
		$this->data["UF_CHECK_RRC"] = $mode;
		return $this;
	}

	function setSuppliersWithRRC($city = "MSK", array $suppliersId = []){
		if( !in_array($city, ["MSK", "SPB"]) )
			return $this;

		$validSuppliersId = array_keys(T50GlobVars::get("CACHE_SUPPLIERS", $city));
		$suppliersId = array_intersect($suppliersId, $validSuppliersId);
		$this->data["UF_SUPPLIERS_RRC"] = $suppliersId;
		return $this;
	}

	function setParameters(array $parameters = []){
		$this->parameters = [];
		if( empty($parameters) || !isset($parameters[0]))
			return $this;

		$arResult = [];
		foreach($parameters as $blockData){
			$data = [];
			foreach(self::$mapJsCodeToDbCodeValidator as $code => $info){
				list($dbCode, $prepare) = $info;
				$data[$dbCode] = 0;

			    $val = $prepare($blockData[$code]);

				if( $dbCode == "UF_PERCENT" && $val > 100 ){
					$this->log("percent > 100");
					return $this;
				}

				if( $val < 0 ){
					$this->log("${dbCode} value < 0");
					return $this;
				}

				$data[$dbCode] = $val;
			}

		    $arResult[] = $data;
		}

		if( count($arResult) == 1 ){
			$this->parameters = $arResult;
			return $this;
		}

		usort($arResult, function ($a, $b){
			if( $a["UF_MAX_PURCHASE"] == 0 )
				return 1;

			return ($a["UF_MAX_PURCHASE"] > $b["UF_MIN_PURCHASE"] ? 1 : -1);
		});

		$last = end($arResult);
		if( $last["UF_MIN_PURCHASE"] == 0 ){
			$this->log("not valid min entry == max entry == 0");
			return $this;
		}

		for ($i = 0; $i < count($arResult) - 1; $i++) {
			if( $arResult[$i]["UF_MAX_PURCHASE"] != $arResult[$i + 1]["UF_MIN_PURCHASE"] ){
				$this->log("not valid entry range");
				return $this;
			}
		}
		$this->parameters = $arResult;
		return $this;
	}

	function setTitle($title){
		$title = trim(htmlspecialchars($title));
		if( !empty($title) )
			$this->data["UF_TITLE"] = $title;

		return $this;
	}

	function setComment($comment){
		$comment = trim(htmlspecialchars($comment));
		if( !empty($comment) )
			$this->data["UF_COMMENT"] = $comment;

		return $this;
	}

	private function existsTitle($title){
		$filter = ["=UF_TITLE" => $title];
		// $existsForumlas = T50GlobVars::get("FORMULAS");
		if( $this->id > 0 ){
			$filter["!ID"] = $this->id;
			// unset($existsForumlas[$this->id]);
		}
		$existsData = Formula::clas()::getRow(["filter" => $filter]);
		return isset($existsData);
		// return in_array($title, $existsForumlas);
	}

	function save(){
		if( empty($this->data["UF_TITLE"]) ){
			$this->log("empty title");
			return false;
		}

		if( empty($this->data["UF_COMMENT"]) ){
			$this->log("empty comment");
			return false;
		}

		if( $this->existsTitle($this->data["UF_TITLE"]) ){
			$this->log("title \"" . $this->data["UF_TITLE"] . "\" exists");
			return false;
		}

		if( empty($this->parameters) ){
			$this->log("empty parameters");
			return false;
		}

		if( !empty($this->data["UF_MODE"]) && !is_numeric($this->data["UF_MODE"]) )
			$this->data["UF_MODE"] = $this->modeMap[$this->data["UF_MODE"]]["id"];

		$this->data["UF_MANAGER_ID"] = $GLOBALS["USER"]->getId();
		$this->data["UF_DATE"] = date("d.m.Y");

		if( $this->id > 0 )
			$success = $this->update();
		else
			$success = $this->create();

		if( $success )
			T50GlobVars::get("FORMULAS", true); // reset

		return $success;
	}

	private function create(){
		T50DB::startTransaction();
		$ormClass = Formula::clas();

		$main = $this->data;

		if( count($this->parameters) == 1 )
			$main = array_merge($main, $this->parameters[0]);

		$result = $ormClass::add($main);
		if( !$result->isSuccess() ){
			$this->log("cannot create formula:\n" . var_export($main, true));
			return T50DB::rollback();
		}

		$this->id = $result->getId();

		if( !$this->makeChilds() )
			return T50DB::rollback();


		return T50DB::commit();
	}

	private function update(){
		list($main, $oldChilds) = $this->loadData($this->id, true);
		if( $main == null ){
			$this->log("main formula not found (with id {$this->id})");
			return false;
		}

		T50DB::startTransaction();
		$ormClass = Formula::clas();

		foreach($oldChilds as $child){
			if( !$ormClass::delete($child["ID"])->isSuccess() ){
				$this->log("cannot delete child data with id " . $child["ID"]);
				return T50DB::rollback();
			}
		}

		$main = array_merge($main, $this->data);

		if( count($this->parameters) == 1 )
			$main = array_merge($main, $this->parameters[0]);


		if( !$ormClass::update($this->id, $main)->isSuccess() ){
			$this->log("cannot update main data for id {$this->id}:\n" . var_export($main, true));
			return T50DB::rollback();
		}

		if( !$this->makeChilds() )
			return T50DB::rollback();

		return T50DB::commit();
	}

	function delete(int $id){
		if( $id <= 0 )
			return false;

		$items = $this->loadData($id);
		if( empty($items) )
			return true;

		T50DB::startTransaction();
		$ormClass = Formula::clas();
		foreach($items as $item){
			if( !$ormClass::delete($item["ID"])->isSuccess() ){
				$this->log("cannot delete item data with id " . $item["ID"]);
				return T50DB::rollback();
			}
		}
		T50DB::commit();
		T50GlobVars::get("FORMULAS", true); // reset
		return true;
	}

	private function makeChilds(){
		if( count($this->parameters) <= 1 )
			return true;

		if( $this->id <= 0 )
			return false;

		foreach($this->parameters as $parameters){
		    $newChild = ["UF_PARENT_ID" => $this->id];
		    $newChild = array_merge($newChild, $parameters);

		    if( !Formula::clas()::add($newChild)->isSuccess() ){
		    	$this->log("cannot create child data for main id {$this->id}:\n" . var_export($newChild, true));
				return false;
		    }
		}
		return true;
	}
}