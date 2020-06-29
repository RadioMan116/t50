<?php

namespace OrdersDefaultComponent;
use rat\agregator\Delivery;
use rat\agregator\Order;
use Agregator\Order\Utils\OrderPropertyBuilder;
use Agregator\Components\Traits\ComponentDI;

class Filter
{
    use ComponentDI;

	private $input;
    private $filter = array();
    private $innerFilter = array();
    private $propertyBuilder;

    function __construct(){
        $this->propertyBuilder = new OrderPropertyBuilder;
    }

    function setInput(\StdClass $input){
        $this->input = $input;
        return $this;
    }

    function getFilter(){
        $propertyFilter = $this->getPropertyBuilder()->getFilter();
        $filter = array_merge($this->filter, $propertyFilter);
        return $filter;
    }

    function getInnerFilter(){
        return $this->innerFilter;
    }

    function getPropertyBuilder(){
        return $this->propertyBuilder->build();
    }

    function build(){
        $initialData = $this->InitialData->getData();

        if( isset($this->input->all_orders_with_unid) ){

            $this->filter["BK.UF_PRODUCT_ID"] = $this->input->all_orders_with_unid;
            // .... constraint by date
            return $this;
        }

        $orderFlagsCodeId = array_flip(Order::getEnum("UF_FLAGS"));

        $flags = [];

        if( empty($this->input->shop) ){
            $this->filter["UF_SHOP"] = array_column($initialData["SHOP"]["items"], "ID");
        } else {
            $this->filter["UF_SHOP"] = $this->input->shop;
        }

        if( !empty($this->input->status) )
            $this->filter["UF_STATUS"] = $this->input->status;

        if( !empty($this->input->source) )
            $this->filter["UF_SOURCE"] = $this->input->source;

        if( !empty($this->input->manager) )
            $this->filter["UF_MANAGER_ID"] = $this->input->manager;

        if( !empty($this->input->supplier) )
            $this->filter["BK.UF_SUPPLIER"] = $this->input->supplier;

        if( !empty($this->input->provider) )
            $this->filter["INST.UF_PROVIDER"] = $this->input->provider;

        if( isset($this->input->region) )
            $this->filter["UF_CITY"] = $this->input->region;

        if( isset($this->input->commission_recived) ){
            $code = ($this->input->commission_recived ? "!" : "") . "BK.UF_COM_SUPPLIER";
            $this->filter[$code] = false;
            $this->innerFilter[$code] = false;
        }

        // if( $this->input->agency_contract === true )
        //     $this->filter["XXX"] = $this->input->agency_contract;

        // if( $this->input->unlinked === true )
        //     $this->filter["UF_MANAGER_ID"] = 0;

        if( isset($this->input->test) )
            $this->filter["UF_TEST"] = ( $this->input->test ? 1 : 0 );

        if( $this->input->shipment_tk === true ){
            $this->filter["DL.UF_CONDITIONS"] =
                Delivery::getEnum("UF_CONDITIONS", false)["delivery_tk"]["id"];
        }

        if( isset($this->input->official) ){
            $this->filter[( $this->input->official ? "!" : "" ) . "AC.UF_OFFICIAL_OUR"] = false;
        }

        if( !empty($this->input->pay_type) )
            $this->filter["BK.UF_PAYMENT_TYPE"] = $this->input->pay_type;

        if( isset($this->input->complaint) ){
            $this->filter["!CLAIM.UF_DATE_START"] = null;
            if( $this->input->complaint == "open" ){
                $this->filter["CLAIM.UF_DATE_FINISH"] = null;
            } else {
                $this->filter["!CLAIM.UF_DATE_FINISH"] = null;
            }
        }

        if( isset($this->input->date_create_from) )
            $this->filter[">=UF_DATE_CREATE"] = $this->input->date_create_from;

        if( isset($this->input->date_create_to) )
            $this->filter["<=UF_DATE_CREATE"] = $this->input->date_create_to . " 23:59:59";

        if( isset($this->input->date_delivery_from) )
            $this->filter[">=DL.UF_DATE"] = $this->input->date_delivery_from;

        if( isset($this->input->date_delivery_to) )
            $this->filter["<=DL.UF_DATE"] = $this->input->date_delivery_to . " 23:59:59";

        if( isset($this->input->date_account_from) )
            $this->propertyBuilder->setFilter(["date >=DATE_INVOICE" => $this->input->date_account_from]);


        if( isset($this->input->date_account_to) )
            $this->propertyBuilder->setFilter(["date <=DATE_INVOICE" => $this->input->date_account_to]);

        // flags
        if( !empty($flags) )
            $this->filter["UF_FLAGS"] = $flags;

        return $this;
    }
}
