<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$component = $this->getComponent();

$pagenator = new class($component) {
    const SHIFT = 5;
    private $component;

    private $start;
    private $stop;

    function __construct($component){
        $this->component = $component;
    }

    function getPages(){
        $config = $this->getConfig();
        if( $config->end <= 1 )
            return [];

        $arResult = [];
        $arResult[] = $this->getPage($config->start);

        if( $config->startGap )
            $arResult[] = $this->getPage(false);

        if( $config->useInner ){
            for ($page =  $config->innerStart; $page <= $config->innerEnd; $page++) {
                $arResult[] = $this->getPage($page);
            }
        }

        if( $config->endGap )
            $arResult[] = $this->getPage(false);

        $arResult[] = $this->getPage($config->end);

        return $arResult;
    }

    function getSizes(){
        $sizes = array();

        $pageSizes = $this->component->arResult["PAGE_SIZES"];
        $pageSizes[] = "all";
        foreach($pageSizes as $size){
            $page = ( $size == "all" ? "all" : $this->component->arResult["CURRENT_PAGE"] );
            $active = (
                ( $this->component->arResult["ALL_RECORDS"] && $size == "all" )
                ||
                ( !$this->component->arResult["ALL_RECORDS"] && $size == $this->component->arResult["PAGE_SIZE"] )
            );

            $sizes[] = [
                "active" => $active,
                "text" => ( $size == "all" ? "Все" : $size ),
                "url" => $this->getUrl($page, $size),
                "ajax_data" => $this->getAjaxData($page, $size),
            ];
        }

        return $sizes;
    }

    function getUrl($page, $size = 0){
        $size = ( $size > 0 ? $size : $this->component->arResult["PAGE_SIZE"] );
        $url = $this->component->replaceUrlTemplate($page, $size);
        $url = htmlspecialcharsbx($url);
        if( isset($this->component->arParams["ANCHOR"]) )
            $url .= "#" . $this->component->arParams["ANCHOR"];
        return $url;
    }

    function getAjaxData($page, $size = 0){
        // static $name;
        // $name = $name ?? $this->component->getTemplate()->getName();
        $name = $this->component->arResult["ID"];
        $size = ( $size > 0 ? $size : $this->component->arResult["PAGE_SIZE"] );
        return ["name" => $name, "page" => $page, "size" => $size];
    }

    private function getPage($page){
        if( $page === false )
            return ["is_gap" => true];

        return [
            "active" => ($page == $this->component->arResult["CURRENT_PAGE"]),
            "text" => $page,
            "url" => $this->getUrl($page),
            "ajax_data" => $this->getAjaxData($page, $size),
        ];
    }


    private function getConfig(){
        $currentPage = $this->component->arResult["CURRENT_PAGE"];
        $pageCount = $this->component->arResult["PAGE_COUNT"];

        $config = new \StdClass;
        $config->useInner = false;
        $config->startGap = false;
        $config->endGap = false;
        $config->start = 1;
        $config->end = $pageCount;

        if( $pageCount < 3 )
            return $config;

        $config->useInner = true;

        $innerStart = $currentPage - self::SHIFT;
        $innerEnd = $currentPage + self::SHIFT;

        if( ($innerStart - 1) > 2 ){
            $config->startGap = true;
        } else {
            $innerStart = 2;
        }

        if( ($pageCount - $innerEnd) > 2 ){
            $config->endGap = true;
        } else {
            $innerEnd = $pageCount - 1;
        }

        $config->innerStart = $innerStart;
        $config->innerEnd = $innerEnd;

        return $config;
    }

};
$arResult["ITEMS"] = $pagenator->getPages();
$arResult["SIZES"] = $pagenator->getSizes();