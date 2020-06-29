<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Agregator\Components\BaseAjaxComponent;
use Agregator\Components\Traits\ComponentDI;
use Agregator\Manager\Manager;

class CatalogElementComponent extends BaseAjaxComponent
{
	use ComponentDI;

	// manual
	function actionChangeManual(){
		if( !Manager::canChangeProductCard() ) die();
		$this->Ajax_EditManual->exec();
	}


	// formula
	function actionUpdateFormula(){
		if( !Manager::canChangeProductCard() ) die();
		$this->Ajax_UpdateFormula->update();
	}

	function actionDeleteFormula(){
		if( !Manager::canChangeProductCard() ) die();
		$this->Ajax_UpdateFormula->delete();
	}

	function actionLoadFormula(){
		$this->Ajax_FormulaInfo->loadFormula();
	}

	function actionLoadFormulasInfo(){
		$this->Ajax_FormulaInfo->loadFormulasInfo();
	}

	function actionSetFormulaForProducts(){
		if( !Manager::canChangeProductCard() ) die();
		$this->Ajax_UpdateProducts->changeFormula();
	}

	function actionGetUnidsByFormula(){
		if( !Manager::canChangeProductCard() ) die();
		$this->Ajax_UpdateProducts->getUnidsByFormula();
	}

	function actionRecalcProducts(){
		if( !Manager::canChangeProductCard() ) die();
		$this->Ajax_UpdateProducts->recalcProducts();
	}

	function actionSetFormulaForBrand(){
		if( !Manager::canChangeProductCard() ) die();
		$this->Ajax_UpdateProducts->setFormulaForBrand();
	}


	// comments
	function actionCommentsLoad(){
		$this->includeTemplate("comments", $this->Ajax_Comments->load());
	}

	function actionUpdateComment(){
		if( !Manager::canUpdateProductComments() ) die();
		$this->Ajax_Comments->update();
	}


	// basket
	function actionAddToBasket(){
		$this->Ajax_Basket->add();
	}


	// discontinued and analogs
	function actionAnalogs(){
		$this->Ajax_Discontinued->loadAnalogs();
	}

	function actionAnalog(){
		$this->includeTemplate("analog", $this->Ajax_Discontinued->loadAnalog());
	}

	function actionSetAnalog(){
		$this->Ajax_Discontinued->setAnalog();
	}

	function actionSetDiscontinued(){
		if( !Manager::canChangeProductCard() ) die();
		$this->Ajax_Discontinued->setDiscontinued();
	}
}
