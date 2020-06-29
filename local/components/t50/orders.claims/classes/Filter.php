<?php

namespace OrdersClaimsComponent;

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

        $this->filter["BK.UF_CLAIM"] = 1;
        $this->filter["ORD.UF_TEST"] = 0;

        if( !isset($this->input) )
            return;

        if( isset($this->input->order) )
            $this->filter["UF_ORDER_ID"] = $this->input->order;

        if( isset($this->input->shop) )
            $this->filter["ORD.UF_SHOP"] = $this->input->shop;

        if( isset($this->input->supplier) )
            $this->filter["BK.UF_SUPPLIER"] = $this->input->supplier;

        if( isset($this->input->manager) )
            $this->filter["UF_MANAGER_ID"] = $this->input->manager;

        if( isset($this->input->open) )
            $this->filter[( $this->input->open ? "" : "!" ) . "UF_DATE_FINISH"] = null;

        if( isset($this->input->date_request) )
            $this->filter["UF_DATE_REQUEST"] = $this->input->date_request;

        if( isset($this->input->date_result) )
            $this->filter["UF_DATE_FINISH"] = $this->input->date_result;

        if( isset($this->input->unid) )
            $this->filter["BK.UF_PRODUCT_ID"] = $this->input->unid;

        if( isset($this->input->client) ){
            $this->filter[] = array(
                'LOGIC' => 'OR',
                '%=CL.UF_FIO' => "%{$this->input->client}%",
                '%=CL.UF_PHONE' => "%{$this->input->client}%",
                '%=CL.UF_FIO2' => "%{$this->input->client}%",
                '%=CL.UF_PHONE2' => "%{$this->input->client}%",
                '%=CL.UF_REQUISITES' => "%{$this->input->client}%",
                '%=CL.UF_EMAIL' => "%{$this->input->client}%",
                '%=CL.UF_STREET' => "%{$this->input->client}%",
            );
        }
    }
}

