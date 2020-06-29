<?php

namespace CatalogDefaultComponent;

use T50GlobVars;
use rat\agregator\Product;
use rat\agregator\ProductPrice;

class Filter
{
    private $input;
    private $filter = array();

    function setInput(\StdClass $input){
        $this->input = $input;
    }

    function getFilter(){
        return $this->filter;
    }

    function build(){
        $cities = T50GlobVars::get("HLPROPS")[ProductPrice::getTableName()]["UF_CITY"];

        $this->filter["PR.UF_CITY"]= $cities["MSK"]["id"];

        if( isset($this->arParams["SHOP"]) && $this->arParams["SHOP"]["ID"] > 0 ){
            $shopId = (int) $this->arParams["SHOP"]["ID"];
            $this->filter["UF_SHOPS"] = $shopId;
            $this->filter["PR.UF_SHOP"] = $shopId;
        }

        if( !isset($this->input) )
            return;

       if( !empty($this->input->only_model) ){
            $this->filter["?UF_MODEL_PRINT"] = $this->input->only_model;
            return;
       }

        if( isset($this->input->shop) )
            $this->filter["PR.UF_SHOP"] = $this->input->shop;

        if( isset($this->input->city) )
            $this->filter["PR.UF_CITY"] = $cities[$this->input->city]["id"];

        if( isset($this->input->brand) )
            $this->filter["UF_BRAND"] = $this->input->brand;

        if( !empty($this->input->category) )
            $this->filter["UF_CATEGORIES"] = $this->input->category;

        if( !empty($this->input->avail) )
            $this->filter["=AVAIL"] = $this->input->avail;

        if( isset($this->input->sale_from) )
            $this->filter[">=SALE"] = $this->input->sale_from;

        if( isset($this->input->sale_to) )
            $this->filter["<=SALE"] = $this->input->sale_to;

        if( isset($this->input->purchase_from) )
            $this->filter[">=PR.UF_PRICE_PURCHASE"] = $this->input->purchase_from;

        if( isset($this->input->purchase_to) )
            $this->filter["<=PR.UF_PRICE_PURCHASE"] = $this->input->purchase_to;

        if( isset($this->input->commission_from) )
            $this->filter[">=COMMISSION"] = $this->input->commission_from;

        if( isset($this->input->commission_to) )
            $this->filter["<=COMMISSION"] = $this->input->commission_to;

        if( isset($this->input->formula) )
            $this->filter["PR.UF_FORMULA"] = $this->input->formula;


        if( isset($this->input->price_mode) )
            $this->filter["=PR.UF_MANUAL_PRICE"] = ( $this->input->price_mode ? 1 : 0 );

        if( isset($this->input->avail_mode) )
            $this->filter["=PR.UF_MANUAL_AVAIL"] = ( $this->input->avail_mode ? 1 : 0 );

        if( !empty($this->input->model) )
            $this->filter["?UF_MODEL_PRINT"] = $this->input->model;

        if( isset($this->input->delivery) )
            $this->filter["PR.UF_FLAG_FREE_DELIVER"] = ( $this->input->delivery ? 1 : 0 );

        if( isset($this->input->instal) )
            $this->filter["PR.UF_FLAG_FREE_INSTALL"] = ( $this->input->instal ? 1 : 0 );

        if( isset($this->input->new) )
            $this->filter["UF_FLAG_NEW"] = ( $this->input->new ? 1 : 0 );

        if( isset($this->input->build_in) )
            $this->filter["UF_FLAG_BUILD_IN"] = ( $this->input->build_in ? 1 : 0 );
    }
}

