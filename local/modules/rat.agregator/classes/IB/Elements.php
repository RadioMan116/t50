<?php
namespace Agregator\IB;

use \Bitrix\Main\Loader;

class Elements extends Query
{

	function __call($method, $args){
		$validMethods = array(
			// get list
			"getListNext",
			"getListNextCache",
			"getListFetch",
			"getListFetchCache",

			// get list by id
			"getListNextById",
			"getListNextByIdCache",
			"getListFetchById",
			"getListFetchByIdCache",

			// get one
			"getOneNext",
			"getOneNextCache",
			"getOneFetch",
			"getOneFetchCache",

			// get one by id
			"getOneNextById",
			"getOneNextByIdCache",
			"getOneFetchById",
			"getOneFetchByIdCache",
		);
		if( !in_array($method, $validMethods) )
			throw new \RuntimeException("invalid method {$method}");

		return $this->execute($method, $args);
	}

	private function execute($methodName, $args){

		$method = ( substr_count($methodName, "Next") ? "GetNext" : "Fetch" );
		$byId = (bool) substr_count($methodName, "ById");
		$limit = ( isset($args[0]) ? $args[0] : $this->limit );
		if( substr_count($methodName, "One") )
			$limit = 1;
		$useCache = (bool) substr_count($methodName, "Cache");
		$hasIndex = !empty($this->index);
		$hasColumn = !empty($this->column);

		if( $byId ){
			$id = $args[0];
			if( \T50ArrayHelper::isInt($id) ){
				$limit = count($id);
			} elseif( (int) $id > 0 ) {
				$limit = 1;
				unset($this->filter["ACTIVE"]);
			}
			$this->filter["ID"] = $id;
		}

		$this->limit($limit);

		if( $useCache ){
			$queryHash = $this->getQueryHash();
			$cacheKey = "Agregator\\IB\\" . $methodName . $queryHash;
			if( ($arResult = $this->getBxCache($cacheKey)) != null )
				return $arResult;
		}

		$res = $this->get();

		$arResult = array();

		while( $result = $res->$method() ){
			$value = $result;
			if( $hasColumn )
				$value = $result[$this->column];

			if( $hasIndex ){
				$arResult[$result[$this->index]] = $value;
			} else {
				$arResult[] = $value;
			}
		}

		if( $limit === 1  ){
			if( empty($arResult) )
				$arResult = null;
			else
				$arResult = current($arResult);
		}

		if( $useCache )
			return $this->saveBxCache($cacheKey, $arResult);

		return $arResult;
	}
}