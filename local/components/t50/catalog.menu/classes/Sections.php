<?php

namespace CatalogMenuComponent;
use T50GlobCache;

class Sections
{
    const CHUNK_LIMIT = 24;
    private $shopCode = "all";

    function getData(){
        $data = $this->load();
        // $data = T50GlobCache::getBx([$this, "load"], "MENU_SECTIONS", 360000);
        array_map([$this, "prepareUrl"], $data);
        return $data;
    }

    private function prepareUrl($section){
        $section["URL"] = "/catalog/{$this->shopCode}/{$section['CODE']}/";
        return $section;
    }

    function load(){
        $chunkHalf = round(self::CHUNK_LIMIT / 2);
        $sections = \T50GlobVars::get("CACHE_CATEGORIES");
        $sections = array_chunk($sections, self::CHUNK_LIMIT);
        foreach($sections as $k => $chunk){
            for ($i = $chunkHalf; $i < self::CHUNK_LIMIT; $i++) {
                $sections[$k][$i]["HIDDEN"] = 1;
            }
        }
        return $sections;
    }

}
