<?php

namespace CatalogMenuComponent;
use Agregator\IB\Elements;
use T50GlobCache;

class Shops
{
	private $shops;

    function getData(){
    	return $this->load();
    	// return T50GlobCache::getBx([$this, "load"], "MENU_SHOPS", 360000);
    }

    function load(){
    	$elements = new Elements("shops");
		$shops = $elements
			->sort(["name" => "asc", "left_margin"=>"asc"])
			->select(["NAME", "CODE", "PREVIEW_PICTURE"])
			->props(["CITIES", "FORMULA", "OFFICIAL_NAME", "OFFICIAL", "HTTP_HOST"])
			->getListFetch();
		$shops = array_map([$this, "prepareShop"], $shops);
		return $shops;
    }

    private function prepareShop($shop){
    	if( $shop["PREVIEW_PICTURE"] > 0 )
    		$shop["PREVIEW_PICTURE"] = \CFile::GetPath($shop["PREVIEW_PICTURE"]);

    	$parseUrl = parse_url($shop["PROPERTY_HTTP_HOST_VALUE"]);
    	$shop["HOST"] = $parseUrl["host"];

    	return $shop;
    }
}
