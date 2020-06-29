<?php

namespace Agregator\Order;

use T50ArrayHelper;
use T50DB;
use T50GlobVars;
use rat\agregator\File;
use CFile;

class Docs
{
	use Traits\Log;

	private $subFolder = "order_documents";

	function loadAll(int $orderId){
		return $this->load($orderId);
	}

	function loadByType(int $orderId, $type){
		$result = $this->load($orderId, $type);
		if( isset($result[$type]) )
			return $result[$type];

		return [];
	}

	private function load($orderId, $type = null){
		if( $orderId <= 0 )
			return $this->logError("invalid orderId {$orderId}");

		$filter = ["UF_ORDER_ID" => $orderId];

		if( isset($type) ){
			$typeId = File::getEnum("UF_TYPE", false)[$type]["id"];
			if( $typeId <= 0 )
				return $this->logError("undefined file type '{$type}'");
			$filter["UF_TYPE"] = $typeId;
		}

		$arResult = [];
		$res = File::clas()::getList(compact("filter"));
		while( $result = $res->Fetch() ){
			$result = $this->detailDocsRow($result);
			$code = $result["TYPE"];
			if( !isset($arResult[$code]) )
				$arResult[$code] = [];

			$arResult[$code][] = $result;
		}

		return $arResult;
	}

	private function detailDocsRow($docsRow){
		static $types;
		$types = $types ?? File::getEnum("UF_TYPE");

		if( empty($docsRow["UF_FILE"]) )
			return $docsRow;

		$fileInfo = CFile::GetFileArray($docsRow["UF_FILE"]);
		$docsRow["FILE_DATA"] = [
			"id" => $fileInfo["ID"],
			"title" => $fileInfo["ORIGINAL_NAME"],
			"path" => $fileInfo["SRC"],
		];

		$docsRow["TYPE"] = $types[$docsRow["UF_TYPE"]];
		$docsRow["CAN_DELETE"] = ( $GLOBALS["USER"]->getId() == $docsRow["UF_MANAGER_ID"] );

		return $docsRow;
	}

	function save(int $orderId, $type, array $uloadFileData){
		if( $orderId <= 0 )
			return $this->logError("invalid orderId {$orderId}");

		$typeId = File::getEnum("UF_TYPE", false)[$type]["id"];
		if( $typeId <= 0 )
			return $this->logError("undefined file type '{$type}'");

		$uloadFileData["name"] = str_replace("%", "", $uloadFileData["name"]);
		if( empty($uloadFileData["name"]) || empty($uloadFileData["tmp_name"]) || $uloadFileData["error"] != 0 )
			return $this->logError("invalid file data");

        $uloadFileData["MODULE_ID"] = "rat.agregator";

		T50DB::startTransaction();

		$fileId = (int) CFile::SaveFile($uloadFileData, $this->subFolder);
		if( $fileId <= 0 ){
			T50DB::rollback();
			return $this->logError("cannot create file with params:\n" . var_export($uloadFileData, true));
		}

		$result = File::clas()::add([
			"UF_MANAGER_ID" => $GLOBALS["USER"]->getId(),
			"UF_DATE" => date("d.m.Y H:i:s"),
			"UF_FILE" => $fileId,
			"UF_TYPE" => $typeId,
			"UF_ORDER_ID" => $orderId,
		]);

		if( $result->isSuccess() )
			return T50DB::commit();

		return T50DB::rollback();
	}

	function delete(int $fileId, int $orderId, $type){
		$data = $this->loadByType($orderId, $type);
		$rowForDelete = T50ArrayHelper::find($data, function ($docsRow) use($fileId){
			return $docsRow["UF_FILE"] == $fileId;
		});

		if( !isset($rowForDelete) )
			return $this->logError("not found row for delete by orderId {$orderId}, type {$type}, fileId {$fileId}");

		if( !$rowForDelete["CAN_DELETE"] )
			return $this->logError("access deined orderId {$orderId} and type {$type} (foreign manager)");

		T50DB::startTransaction();

		CFile::Delete($fileId);
		$result = File::clas()::delete($rowForDelete["ID"]);
		if( $result->isSuccess() )
			return T50DB::commit();

		return T50DB::rollback();
	}

	private function logError($message){
		$this->log($message);
		return false;
	}

}