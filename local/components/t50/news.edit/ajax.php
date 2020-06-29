<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Agregator\Components\BaseAjaxComponent;
use Agregator\Components\Traits\ComponentDI;
use Agregator\Manager\Manager;

class NewsEditComponent extends BaseAjaxComponent
{
	use ComponentDI;

	function beforeAction(){
		if( !Manager::canWorkWithNews() )
			die();
	}

	function actionUploadFile(){
		$valid = $this->prepare([
				"id" => "intval",
			])->validate([
				"id" => "positive",
				"file" => "File|mimes:xls,xlsx,doc,docx,pdf,sxw,sxc,sxd,sxi,stw,stc,std,sti,ods,jpg,jpeg,png,odt,rtf,rtx,csv,tif,mxl,txt,zip,rar|max:5120", // 5 Mb
			]);

		if( !$valid )
			die();

		if( !$this->FilesData->saveToArticle($this->input->id, $this->input->file) )
			die();

		$this->includeTemplate("files", ["FILES" => $this->FilesData->loadByArticleId($this->input->id)]);
	}

	function actionDeleteFile(){
		$valid = $this->prepare([
				"id, prop_id" => "intval",
			])->validate([
				"id, prop_id" => "positive",
			]);

		if( !$valid )
			die();

		if( !$this->FilesData->deleteFromArticle($this->input->id, $this->input->prop_id) )
			die();

		$this->includeTemplate("files", ["FILES" => $this->FilesData->loadByArticleId($this->input->id)]);
	}
}
