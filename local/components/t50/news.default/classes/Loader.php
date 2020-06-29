<?php

namespace NewsDefaultComponent;

use Agregator\IB\Elements;
use Agregator\Components\Traits\ComponentDI;
use T50GlobVars;
use T50ArrayHelper;
use CFile;
use Agregator\Manager\JSON\ManagerNews;

class Loader
{
    use ComponentDI;

    function loadFixed(){
		$news = $this->getElements(["!PROPERTY_FIX_IN_HEADER" => false]);
    	return $this->getList($news);
    }

    function loadUnread(){
    	$managerNews = new ManagerNews();
        $unreadNews = $managerNews->getUnread();
        $filter = ["ID" => ( empty($unreadNews) ? false : $unreadNews )];
        $news = $this->getElements($filter);
    	return $this->getList($news);
    }

    function loadByFilter(Filter $filter){
    	$news = $this->getElements($filter->getFilter());
    	return $this->getList($news);
    }

    private function getElements(array $filter = array()){
    	$news = new Elements("news");
    	return $news
            ->sort(["ID" => "DESC"])
            ->filter($filter)
    		->select("NAME", "CODE", "DETAIL_TEXT", "PREVIEW_TEXT", "CREATED_BY", "DATE_CREATE")
    		->props("TARGET_MAN_GROUPS", "THEME", "BRAND", "FILES");
    }

    private function getList(Elements $news){
		$news->setNav(4, 5);
    	$res = $news->get();
		$nav = $news->getNavPrint();
		$items = [];
    	while( $result = $res->Fetch() )
    		$items[] = $this->prepareRow($result);

    	return ["ITEMS" => $items, "NAV" => $nav];
    }

    private function prepareRow(array $item){
        return array(
            "ID" => $item["ID"],
            "EDIT_URL" => "/news/edit/" . $item["CODE"] . "/",
            "TITLE" => $item["NAME"],
            "AUTHOR" => $this->getManagerName($item["CREATED_BY"]),
            "DATE_CREATE" => substr($item["DATE_CREATE"], 0, 10),
            "GROUPS" => $this->prepareGroups($item["PROPERTY_TARGET_MAN_GROUPS_VALUE"]),
            "TEXT" => $item["DETAIL_TEXT"],
            "COMMENT" => $item["PREVIEW_TEXT"],
            "IS_FAVORITE" => $this->isFavorite($item["ID"]),
            "FILES" => $this->getFilesLinks($item["PROPERTY_FILES_VALUE"]),
            "BRAND" => $this->prepareBrand($item["PROPERTY_BRAND_VALUE"]),
        );
    }

    private function getFilesLinks($filesId){
    	$files = array();
    	foreach($filesId as $fileId){
    		$fileInfo = CFile::GetFileArray($fileId);
    	    $files[] = array(
    	    	"TITLE" => $fileInfo["ORIGINAL_NAME"],
				"PATH" => $fileInfo["SRC"],
    	    );
    	}
    	return $files;
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

    private function prepareBrand($brandId){
        $brands = $this->InitialData->getData()["BRANDS"];
        return $brands[$brandId];
    }

    private function isFavorite(int $id){
    	static $favorites;
    	if( !isset($favorites) ){
    		$managerNews = new ManagerNews();
    		$favorites = array_fill_keys($managerNews->getFavorite(), true);
    	}
    	return ( isset($favorites[$id]) ? 1 : 0 );
    }

}