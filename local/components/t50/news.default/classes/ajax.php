<?php

namespace NewsDefaultComponent;

use Agregator\IB\Elements;
use Agregator\Components\Traits\ComponentDI;
use T50GlobVars;
use T50ArrayHelper;

class Loader
{
    use ComponentDI;

	private $filter;
	private $nav;

	function setFilter(Filter $filter){
        $this->filter = $filter;
        return $this;
    }

    function loadFixed(){

    }

    function loadUnread(){

    }

    function loadByFilter(){
    	$news = new Elements("news");
    	$res = $news
    		->setNav(10, "фывфыв")
    		->sort(["ID" => [1704, 1706]])
    		->select("NAME", "DETAIL_TEXT", "CREATED_BY", "DATE_CREATE")
    		->filter($this->filter->getFilter())
    		->props("TARGET_MAN_GROUPS", "THEME", "BRAND")
    		->get();
    	$this->nav = $news->getNavPrint();
    	$arResult = [];
    	while( $result = $res->Fetch() )
    		$arResult[] = $this->prepareRow($result);

    	return $arResult;
    }

    function getNav(){
    	return $this->nav;
    }

    private function prepareRow(array $item){
        return array(
            "ID" => $item["ID"],
            "TITLE" => $item["NAME"],
            "AUTHOR" => $this->getManagerName($item["CREATED_BY"]),
            "DATE_CREATE" => substr($item["DATE_CREATE"], 0, 10),
            "GROUPS" => $this->prepareGroups($item["PROPERTY_TARGET_MAN_GROUPS_VALUE"]),
            "TEXT" => $item["DETAIL_TEXT"],
        );
    }

    private function getManagerName(int $managerId){
        static $managers;
        $managers = $managers ?? T50GlobVars::get("MANAGERS");
        return $managers[$managerId]["NAME"];
    }

    private function prepareGroups(array $groupsId){
        $groups = $this->InitialData->getData()["GROUPS"];
        return T50ArrayHelper::filterByKeys($groups, $groupsId);
    }

}