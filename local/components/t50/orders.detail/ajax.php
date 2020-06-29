<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Agregator\Components\BaseAjaxComponent;
use Agregator\Components\Traits\ComponentDI;
use Agregator\Manager\Manager;
use Agregator\Order\Order;

class OrdersDetailComponent extends BaseAjaxComponent
{
	use ComponentDI;

	function beforeAction(){
		$orderId = (int) $_POST["order_id"];
		if( $orderId > 0 &&  !Order::hasAccess($orderId) )
			$this->resultJson(false, "access denied");
	}

	### Compiler ###

	function actionLoadAllPrices(){
		$this->AJAX_Compiler->loadAllPrices();
	}

	function actionLoadProfit(){
		$this->AJAX_Compiler->loadProfit();
	}


	### Order ###

	function actionOrderLoad(){
		$this->AJAX_Order->load();
	}

	function actionOrderUpdate(){
		$this->AJAX_Order->update();
	}

	function actionLoadStatic(){
		$this->AJAX_Order->loadStatic();
	}

	function actionTake(){
		$this->AJAX_Order->take();
	}


	### Basket ###

	function actionBasketLoad(){
		$this->AJAX_Basket->load();
	}

	function actionBasketCreate(){
		$this->AJAX_Basket->create();
	}

	function actionBasketSetManual(){
		$this->AJAX_Basket->setManual();
	}

	function actionBasketUpdate(){
		$this->AJAX_Basket->update();
	}

	function actionBasketDelete(){
		$this->AJAX_Basket->delete();
	}


	### Client ###

	function actionClientLoad(){
		$this->AJAX_Client->load();
	}

	function actionClientUpdate(){
		$this->AJAX_Client->update();
	}

	function actionClientSendEmail(){
		$this->AJAX_Client->sendEmail();
	}


	### Delivery ###

	function actionDeliveryLoad(){
		$this->AJAX_Delivery->load();
	}

	function actionDeliveryUpdate(){
		$this->AJAX_Delivery->update();
	}


	### Account ###

	function actionAccountLoad(){
		$this->AJAX_Account->load();
	}

	function actionAccountUpdate(){
		$this->AJAX_Account->update();
	}

	function actionAccountRemoveRow(){
		$this->AJAX_Account->removeRow();
	}


	### Installation prices ###

	function actionInstallationPricesLoadCategories(){
		$this->AJAX_InstallationPrices->loadCategories();
	}

	function actionInstallationPricesLoadServices(){
		$this->AJAX_InstallationPrices->loadServices();
	}

	function actionInstallationPricesLoadService(){
		$this->AJAX_InstallationPrices->loadService();
	}


	### Installation ###

	function actionInstallationLoad(){
		$this->AJAX_Installation->load();
	}

	function actionInstallationUpdate(){
		$this->AJAX_Installation->update();
	}

	function actionInstallationCreate(){
		$this->AJAX_Installation->create();
	}

	function actionInstallationDelete(){
		$this->AJAX_Installation->delete();
	}

	function actionSentRemcity(){
		$this->AJAX_Installation->sentRemcity();
	}


	### Docs ###

	function actionDocsLoad(){
		$this->AJAX_Docs->load();
	}

	function actionUploadDoc(){
		$this->AJAX_Docs->upload();
	}

	function actionDeleteDoc(){
		$this->AJAX_Docs->delete();
	}


	### Comments ###

	function actionCommentsLoad(){
		$this->AJAX_Comments->load();
	}

	function actionCommentsCreate(){
		$this->AJAX_Comments->create();
	}

	function actionCommentsUpdate(){
		$this->AJAX_Comments->update();
	}


	## Fine ###

	function actionFineCreate(){
		$this->AJAX_Fine->create();
	}

	function actionFineUpdate(){
		$this->AJAX_Fine->update();
	}

	function actionFineDelete(){
		$this->AJAX_Fine->delete();
	}

	function actionFineLoad(){
		$this->AJAX_Fine->load();
	}


	## Deduction ###

	function actionDeductionCreate(){
		$this->AJAX_Deduction->create();
	}

	function actionDeductionUpdate(){
		$this->AJAX_Deduction->update();
	}

	function actionDeductionDelete(){
		$this->AJAX_Deduction->delete();
	}

	function actionDeductionLoad(){
		$this->AJAX_Deduction->load();
	}

	function actionDeductionLoadTypes(){
		$this->AJAX_Deduction->loadTypes();
	}


	## Reclamation ###

	function actionReclamationLoadAll(){
		$this->AJAX_Reclamation->loadAll();
	}

	function actionReclamationLoad(){
		$this->AJAX_Reclamation->load();
	}

	function actionReclamationLoadFiles(){
		$this->AJAX_Reclamation->loadFiles();
	}

	function actionReclamationUpdate(){
		$this->AJAX_Reclamation->update();
	}

	function actionReclamationSaveFile(){
		$this->AJAX_Reclamation->saveFile();
	}

	function actionReclamationDeleteFile(){
		$this->AJAX_Reclamation->deleteFile();
	}


	### Mailing ###

	function actionMailingLoadData(){
		$this->AJAX_Mailing->loadData();
	}

	function actionMailingSend(){
		$this->AJAX_Mailing->send();
	}

	function actionMailingLoadHistory(){
		$this->AJAX_Mailing->loadHistory();
	}


	### History ###

	function actionLoadHistory(){
		$this->includeTemplate("history", $this->AJAX_History->load());
	}
}
