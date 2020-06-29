<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Agregator\Manager\Manager;
use Agregator\Components\BaseAjaxComponent;
use Agregator\Components\Traits\ComponentDI;
use Agregator\Components\Traits\ComponentData;
use Agregator\Manager\JSON\ManagerNews;

class NewsDefaultComponent extends BaseAjaxComponent
{
	use ComponentDI;
	use ComponentData;

	function actionSwitchFavorite(){
		$valid = $this->prepare([
				"id" => "intval",
				"value" => "boolean",
			])->validate([
				"id" => "positive",
				"value" => "bool",
			]);

		if( !$valid )
			$this->validateErrors();

		$managerNews = new ManagerNews;
		if( $this->input->value ){
			$success = $managerNews->addFavorite($this->input->id);
		} else {
			$success = $managerNews->removeFavorite($this->input->id);
		}
		$this->resultJson($success);
	}

	function actionSetAsRead(){
		$valid = $this->prepare(["id" => "intval"])->validate(["id" => "positive"]);
		if( !$valid )
			$this->validateErrors();

		$managerNews = new ManagerNews;
		$this->resultJson($managerNews->setAsReaded($this->input->id));
	}
}