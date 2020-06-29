<?php

namespace NewsDefaultComponent;
use Agregator\Manager\JSON\ManagerNews;

class Filter
{
	private $input;
    private $filter = array();
    private $unreadNews = array();

    function __construct(){
        $managerNews = new ManagerNews();
        $this->unreadNews = $managerNews->getUnread();
    }

	function setInput(\StdClass $input){
        $this->input = $input;
        return $this;
    }

    function getFilter(){
        return $this->filter;
    }

    function build(){
        if( !empty($this->unreadNews) )
            $this->filter["!ID"] = $this->unreadNews;

    	if( isset($this->input->brand) )
    		$this->filter["PROPERTY_BRAND"] = $this->input->brand;

    	if( isset($this->input->theme) )
    		$this->filter["PROPERTY_THEME"] = $this->input->theme;

    	if( isset($this->input->group) )
    		$this->filter["PROPERTY_TARGET_MAN_GROUPS"] = $this->input->group;

		if( isset($this->input->date_from) )
    		$this->filter[">=DATE_CREATE"] = $this->input->date_from;

    	if( isset($this->input->date_to) )
    		$this->filter["<=DATE_CREATE"] = $this->input->date_to . " 23:59:59";

    	return $this;
    }
}