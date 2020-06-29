<?php
use Agregator\Manager\Manager;
use Bitrix\Main\Loader;
use ORM\ORMInfo;

define("DBG", (isset($_COOKIE["adedbmgfoicsdfdss"]) &&  $_COOKIE["adedbmgfoicsdfdss"]==="s934498nsddxv6dkss"));

Loader::includeModule('iblock');
Loader::includeModule('highloadblock');
Loader::IncludeModule("rat.agregator");

const AVAIL_IN_STOCK = 1;
const AVAIL_BY_REQUEST = 2;
const AVAIL_OUT_OF_STOCK = 3;
const AVAIL_DISCONTINUED = 4;


if( true ){ // TODO: dected CITY
	T50GlobVars::set("CITY", "MSK");
}

T50GlobVars::set("VALID_NAMES_CITY", array("MSK", "SPB"));
T50GlobVars::set("SITE_ID", "s1");

T50GlobVars::set("DATE_FORMAT", function(){
	$siteId = T50GlobVars::get("SITE_ID");
	return $GLOBALS["DB"]->DateFormatToPHP(CSite::GetDateFormat("SHORT", $siteId));
});
T50GlobVars::set("DATE_FORMAT_FULL", function(){
	$siteId = T50GlobVars::get("SITE_ID");
	return $GLOBALS["DB"]->DateFormatToPHP(CSite::GetDateFormat("FULL", $siteId));
});


T50GlobVars::set("CACHE_SHOPS", function(){
	$elements = new Agregator\IB\Elements("shops");
	return $elements->sort(["NAME" => "ASC"])->setIndex("ID")->select("NAME", "CODE")->props("CITIES", "FORMULA", "HTTP_HOST", "GROUP")->getListFetchCache();
});

T50GlobVars::set("CACHE_CATEGORIES", function($index = "ID"){
	$elements = new Agregator\IB\Elements("categories");
	return $elements->sort(["NAME" => "ASC"])->setIndex($index)->select("NAME", "CODE")->getListFetchCache();
});

T50GlobVars::set("CACHE_SUPPLIERS", function($byCity = null, $reset = false){
	if( $reset )
		T50GlobVars::del("CACHE_SUPPLIERS");
	$result = T50GlobCache::getRds(function(){
		$elements = new Agregator\IB\Elements("suppliers");
		return $elements->sort(["NAME" => "ASC"])->setIndex("ID")
			->select("NAME", "CODE", "DATE_ACTIVE_FROM")
			->props("CITY", "MARKET_ID", "MAIN_STORAGE")
			->getListFetch();
	}, "CACHE_SUPPLIERS", 360000, $reset);

	if( empty($byCity) )
		return $result;

	return array_filter($result, function ($item) use ($byCity){
		return $item["PROPERTY_CITY_VALUE"] == $byCity;
	});
});

T50GlobVars::set("CACHE_BRAND_NAMES", function(){
	$elements = new Agregator\IB\Elements("brands");
	$data = $elements->sort(["NAME" => "ASC"])->select("NAME")->setIndex("ID")->getListFetchCache();
	return array_map(function($item){
		return $item["NAME"];
	}, $data);
});

$iBlocks = new Agregator\IB\IBlocks;
$lpropsList = array("SHOPS", "SUPPLIERS", "PRODUCTS", "INSTALLATION", "NEWS");
foreach($lpropsList as $code){
	T50GlobVars::set("LPROPS_" . $code, function() use($iBlocks, $code) {
		return $iBlocks->getPropsTypeList(strtolower($code));
	});
}

T50GlobVars::set("HL_TABLES", function(){
	return T50GlobCache::getBx(function(){
		return ORMInfo::getHLTables();
	}, "HL_TABLES", 360000);
});

T50GlobVars::set("HLPROPS", function(){
	return T50GlobCache::getBx(function(){
		return ORMInfo::getHLEnum();
	}, "HLPROPS", 360000);
});

T50GlobVars::set("HLFLAGS", function(){
	return T50GlobCache::getBx(function(){
		return ORMInfo::getHlFlagCodes();
	}, "HLFLAGS", 360000);
});

T50GlobVars::set("FIELD_NAMES", function(){
	return T50GlobCache::getBx(function(){
		return ORMInfo::getFieldNames();
	}, "FIELD_NAMES", 3600000);
});

T50GlobVars::set("MANAGERS_GROUPS", function($flip = false){
	$result = T50GlobCache::getBx(function(){
		$groups = \Bitrix\Main\GroupTable::getList(["select" => ["ID", "STRING_ID"]])->fetchAll();
		$groupsIdCode = array_column($groups, "STRING_ID", "ID");
		return $groupsIdCode;
	}, "MANAGERS_GROUPS", 360000);
	return ( $flip ? array_flip($result) : $result );
});

T50GlobVars::set("MANAGERS", function($group = null){
	if( T50GlobVars::isStartedClearCache() ){
		foreach(T50GlobVars::get("MANAGERS_GROUPS") as $groupCode){
			T50GlobCache::getBx(function() use ($groupCode){
				return Manager::getList($groupCode);
			}, "MANAGERS_{$groupCode}", 360000);
		}
	}
	$key = "MANAGERS_" . ( isset($group) ? $group : "ALL" ) ;
	return T50GlobCache::getBx(function() use ($group){
		return Manager::getList($group);
	}, $key, 360000);
});

T50GlobVars::set("FORMULAS", function($reset = false){
	return T50GlobCache::getBx(function(){
		return Agregator\Product\Calculator\FormulaLoader::getFormulas();
	}, "FORMULAS", ( $reset ? 1 : 360000 ));
});
?>
