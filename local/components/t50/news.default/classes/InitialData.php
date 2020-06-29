<?php

namespace NewsDefaultComponent;
use T50GlobVars;
use Agregator\Manager\JSON\ManagerNews;

class InitialData
{
	function getData(){
        static $data;
        if( isset($data) )
            return $data;

        $data = [
            "BRANDS" => [1 => "ВНЕ БРЕНДА"] + T50GlobVars::get("CACHE_BRAND_NAMES"),
            "THEMES" => $this->getThemes(),
            "GROUPS" => $this->getGroups(),
        ];

        return $data;
    }

    private function getGroups(){
        $managerNews = new ManagerNews();
        $groups = $managerNews->getGroups();
        return array_column($groups, "NAME", "ID");
    }

    private function getThemes(){
        $themes = T50GlobVars::get("LPROPS_NEWS")["THEME"];
        return array_flip($themes["VALUES"]);
    }
}
