<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Agregator\Components\BaseAjaxComponent;
use Agregator\Components\Traits\ComponentDI;
use Agregator\Manager\Manager;
use Bitrix\Main\Application;
use Bitrix\Main\Web\Cookie;

class CommonAjaxComponent extends BaseAjaxComponent
{
	// use ComponentDI;

	function actionLoadManagers(){
		$this->prepare(["name" => "null, htmlspecialchars"]);
		$managers = [];
        foreach(T50GlobVars::get("MANAGERS") as $id => $item)
            $managers[] = ["id" => intval($id), "name" => $item["NAME"]];


		if( isset($this->input->name) ){
			$managers = array_filter($managers, function ($manager){
				return preg_match("#{$this->input->name}#iu", $manager["name"]);
			});
			$managers = array_values($managers);
		}

        $this->resultJson(true, "", $managers);
	}

	function actionSetCookie(){
		$valid = $this->prepare(["name, value" => "htmlspecialchars"])->validate(["name, value" => "required"]);
		if( !$valid )
			$this->validateErrors();

		$context = Application::getInstance()->getContext();
		$cookie = new Cookie($this->input->name, $this->input->value, strtotime("6 month"));
		$cookie->setHttpOnly(true);
		$context->getResponse()->addCookie($cookie);
		$context->getResponse()->flush("");
		$this->resultJson(true);
	}

	function actionLoadAvailableShops(){
		$this->prepare(["name" => "null, htmlspecialchars"]);
		$shops = Manager::getAvailableShops();
		if( isset($this->input->name) ){
			$shops = array_filter($shops, function ($name){
				return preg_match("#{$this->input->name}#iu", $name);
			});
		}
		$result = [];
		foreach($shops as $id => $name)
		    $result[] = compact("id", "name");

        $this->resultJson(true, "", $result);
	}
}
