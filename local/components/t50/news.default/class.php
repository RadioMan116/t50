<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();


use Agregator\Components\BaseComponent;
use Agregator\Components\Traits\ComponentDI;
use Agregator\Manager\Manager;


class NewsDefaultComponent extends BaseComponent
{
	use ComponentDI;

	function execFilter(){
		$valid = $this->prepare([
				"brand, theme, group" => "null, intval",
				"date_from, date_to" => "null, htmlspecialchars",
			])->validate([
				"brand, theme, group" => "positive",
				"date_from, date_to" => "date_format: d.m.Y",
			]);

		if( !$valid ){
			$this->arResult["FILTER_ERROR"] = true;
			return;
		}

		$this->Filter->setInput($this->input);
	}

	function executeComponent(){
		$this->execFilter();
		$this->arResult["INITIAL_DATA"] = $this->InitialData->getData();
		$this->arResult["NEWS_FIXED"] = $this->Loader->loadFixed();
		$this->arResult["NEWS_UNREAD"] = $this->Loader->loadUnread();
		$this->arResult["NEWS_BY_FILTER"] = $this->Loader->loadByFilter($this->Filter->build());
		$this->arResult["CAN_EDIT"] = Manager::canWorkWithNews();
		$this->IncludeComponentTemplate();
	}
}