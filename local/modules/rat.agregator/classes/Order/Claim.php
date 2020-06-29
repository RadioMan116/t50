<?php

namespace Agregator\Order;
use T50Date;
use T50GlobVars;
use T50DB;
use CFile;
use Agregator\Manager\Manager;
use rat\agregator\Complaint;

class Claim
{
	use Traits\Log;

	private $orderId;
	private $subFolder = "claims_documents";

    function init(int $orderId){
    	if( $orderId > 0 )
    		$this->orderId = $orderId;

    	return $this;
    }

    function setRequestDate(string $date){
    	if( !empty($date) && !T50Date::check($date, "d.m.Y") )
    		return false;

    	return $this->update("UF_DATE_REQUEST", $date);
    }

    function setAcceptDate(string $date){
    	if( !empty($date) && !T50Date::check($date, "d.m.Y") )
    		return false;

    	return $this->update("UF_DATE_START", $date);
    }

    function setFinishDate(string $date){
    	if( !empty($date) && !T50Date::check($date, "d.m.Y") )
    		return false;

    	return $this->update("UF_DATE_FINISH", $date);
    }

    function setReason(int $id){
    	$arIdCode = Complaint::getEnum("UF_REASON");
		if( $id != 0 && !isset($arIdCode[$id]) )
			return $this->logError("not valid reason id {$id}");

    	return $this->update("UF_REASON", $id);
    }

    function setRequirement(int $id){
    	$arIdCode = Complaint::getEnum("UF_REQUIREMENT");
		if( $id != 0 && !isset($arIdCode[$id]) )
			return $this->logError("not valid requirement id {$id}");

    	return $this->update("UF_REQUIREMENT", $id);
    }

    function setResult(int $id){
    	$arIdCode = Complaint::getEnum("UF_RESULT");
		if( $id != 0 && !isset($arIdCode[$id]) )
			return $this->logError("not valid result id {$id}");

    	return $this->update("UF_RESULT", $id);
    }

    function setErrorType(int $id){
    	$arIdCode = Complaint::getEnum("UF_ERROR_TYPE");
		if( $id != 0 && !isset($arIdCode[$id]) )
			return $this->logError("not valid error type id {$id}");

    	return $this->update("UF_ERROR_TYPE", $id);
    }

    function setDescription(string $description){
    	return $this->update("UF_DESCRIPTION", $description);
    }

    function setResponsibleManager(int $managerId){
    	$managers = T50GlobVars::get("MANAGERS", "sales_managers");
    	if( $managerId != 0 && !isset($managers[$managerId]) )
			return false;

    	return $this->update("UF_MANAGER_ID", $managerId);
    }

    function saveFile(array $uloadFileData){
    	if( !Manager::canWorkWithClaim() || $this->orderId <= 0 )
			return false;

    	$current = Complaint::clas()::getRow(["filter" => ["UF_ORDER_ID" => $this->orderId]]);
    	$currentFiles = $current["UF_FILES"];
        if( empty($currentFiles) )
            $currentFiles = [];

		T50DB::startTransaction();

        $uloadFileData["name"] = str_replace("%", "", $uloadFileData["name"]);
		$fileId = (int) CFile::SaveFile($uloadFileData, $this->subFolder);
		if( $fileId <= 0 ){
			T50DB::rollback();
			return $this->logError("cannot create file with params:\n" . var_export($uloadFileData, true));
		}

		$currentFiles[] = $fileId;
		if( !$this->update("UF_FILES", $currentFiles) )
			return T50DB::rollback();

		return T50DB::commit();
    }

    function deleteFile(int $fileId){
    	if( !Manager::canWorkWithClaim() || $this->orderId <= 0 || $fileId <= 0 )
			return false;

		$current = Complaint::clas()::getRow(["filter" => ["UF_ORDER_ID" => $this->orderId]]);
    	$currentFiles = $current["UF_FILES"];
        if( empty($currentFiles) )
            $currentFiles = [];

		if( !in_array($fileId, $currentFiles) )
			return false;

		$currentFiles = array_diff($currentFiles, [$fileId]);

		T50DB::startTransaction();

		CFile::Delete($fileId);
		if( !$this->update("UF_FILES", $currentFiles) )
			return T50DB::rollback();

		return T50DB::commit();
    }

    function getFiles($currentFiles){
        if( empty($currentFiles) ){
        	$current = Complaint::clas()::getRow(["filter" => ["UF_ORDER_ID" => $this->orderId ?? 0 ]]);
        	$currentFiles = $current["UF_FILES"];
        }
        if( empty($currentFiles) )
            $currentFiles = [];
    	$result = array_map(function ($fileId){
    		$fileInfo = CFile::GetFileArray($fileId);
    		return [
				"id" => $fileInfo["ID"],
				"title" => $fileInfo["ORIGINAL_NAME"],
				"path" => $fileInfo["SRC"],
			];
    	}, $currentFiles);

    	return $result;
    }

    private function update($code, $value){
    	if( !Manager::canWorkWithClaim() || $this->orderId <= 0 )
			return false;

    	$current = Complaint::clas()::getRow(["filter" => ["UF_ORDER_ID" => $this->orderId]]);
    	if( isset($current["ID"]) ){
    		$result = Complaint::clas()::update($current["ID"], [$code => $value]);
    	} else {
    		$data = array(
				"UF_ORDER_ID" => $this->orderId,
				$code => $value
    		);
    		$result = Complaint::clas()::add($data);
    	}

    	return $result->isSuccess();
    }
}
