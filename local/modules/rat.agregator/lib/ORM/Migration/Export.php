<?php

namespace ORM\Migration;

use Bitrix\Main\Entity;
use Bitrix\Highloadblock\DataManager as HLDataManager;
use Bitrix\Highloadblock\HighloadBlockTable as HBT;
use Agregator\Common\ReportXml;

class Export extends Migration
{
	function exec(){
		if( empty($this->entity) ){
			$this->errors[] = "entity not initialized";
			return false;
		}

		$fields = $this->getData();
		$data = array(
			"hiblock" => $this->entity,
			"fields" => $fields,
		);
		$reportXml = new ReportXml;
		$path = "/upload/" . $this->entity["NAME"] . ".xml";
		$path = $_SERVER["DOCUMENT_ROOT"] . $path;
		$reportXml->setFilePath($path);
		$reportXml->setData($data);
		$exported = (bool) $reportXml->export();
		if( !$exported )
			$this->errors[] = "cannot export xml";

		return $exported;
	}
}