<?php

namespace OrdersDetailComponent\AJAX;

use Agregator\Components\BaseAjaxComponent;
use Agregator\Order\InstallationPrices as Prices;
use Agregator\IB\Elements;
use T50GlobVars;
use T50ArrayHelper;

class InstallationPrices extends BaseAjaxComponent
{
	function loadCategories(){
		$installationPrices = new Prices;
		$providers = $installationPrices->getProviders();

		$valid = $this->prepare([
				"provider" => "null, htmlspecialchars",
			])->validate([
				"provider" => function ($code) use ($providers){
					return isset($providers[$code]);
				},
			]);

		if( !$valid )
			$this->validateErrors();

		$provider = $this->input->provider ?? "remcity";
		$categories = $installationPrices->setProvider($provider)->getCategoies();
		$data = array(
			"providers" => T50ArrayHelper::toIndexed($providers, ["val", "title"]),
			"provider" => $provider,
			"categories" => T50ArrayHelper::toIndexed($categories, ["val", "title"]),
		);

		$this->resultJson(true, "", $data);
	}

	function loadServices(){
		$installationPrices = new Prices;
		$providers = $installationPrices->getProviders();

		$valid = $this->prepare([
				"provider" => "htmlspecialchars",
				"category_id" => "intval",
			])->validate([
				"provider" => function ($code) use ($providers){
					return isset($providers[$code]);
				},
				"category_id" => "positive",
			]);

		if( !$valid )
			$this->validateErrors();

		$categories = $installationPrices->setProvider($this->input->provider)->getCategoies();
		if( !isset($categories[$this->input->category_id]) )
			$this->resultJson(false, "unknow category id");


		$services = $installationPrices->setCategory($this->input->category_id)->getServices();
		$services = T50ArrayHelper::toIndexed($services, ["val", "NAME" => "title"]);

		$data = array(
			"category_id" => $this->input->category_id,
			"services" => $services,
		);

		$this->resultJson(true, "", $data);
	}

	function loadService(){
		$this->prepare(["all" => "null, boolean"])->validate(["all" => "bool"]);
		if( $this->input->all ){
			$this->loadAllByService();
			return;
		}

		$installationPrices = new Prices;
		$providers = $installationPrices->getProviders();

		$valid = $this->prepare([
				"provider" => "htmlspecialchars",
				"category_id, service_id" => "intval",
			])->validate([
				"provider" => function ($code) use ($providers){
					return isset($providers[$code]);
				},
				"category_id, service_id" => "positive",
			]);

		if( !$valid )
			$this->validateErrors();

		$categories = $installationPrices->setProvider($this->input->provider)->getCategoies();
		if( !isset($categories[$this->input->category_id]) )
			$this->resultJson(false, "unknow category id");

		$services = $installationPrices->setCategory($this->input->category_id)->getServices();
		$service = $services[$this->input->service_id];
		if( !isset($service) )
			$this->resultJson(false, "unknow service id");

		$data = array(
			"service_id" => $service["ID"],
			"price" => $service["PROPERTY_PRICE_VALUE"],
		);

		$this->resultJson(true, "", $data);
	}

	private function loadAllByService(){
		$installationPrices = new Prices;
		$providers = $installationPrices->getProviders();

		$valid = $this->prepare([
				"provider" => "htmlspecialchars",
				"service_id" => "intval",
			])->validate([
				"provider" => function ($code) use ($providers){
					return isset($providers[$code]);
				},
				"service_id" => "positive",
			]);

		if( !$valid )
			$this->validateErrors();

		$service = (new Elements($this->input->provider))
			->select("IBLOCK_SECTION_ID")->props("PRICE")
			->getOneFetchById($this->input->service_id);

		if( !isset($service) )
			$this->resultJson(false, "service not found");

		$categories = $installationPrices->setProvider($this->input->provider)->getCategoies();
		$services = $installationPrices->setCategory($service["IBLOCK_SECTION_ID"])->getServices();

		$data = array(
			"service_id" => $service["ID"],
			"price" => $service["PROPERTY_PRICE_VALUE"],
			"provider" => $this->input->provider,
			"category_id" => $service["IBLOCK_SECTION_ID"],
			"providers" => T50ArrayHelper::toIndexed($providers, ["val", "title"]),
			"categories" => T50ArrayHelper::toIndexed($categories, ["val", "title"]),
			"services" => T50ArrayHelper::toIndexed($services, ["val", "NAME" => "title"]),
		);

		$this->resultJson(true, "", $data);
	}
}