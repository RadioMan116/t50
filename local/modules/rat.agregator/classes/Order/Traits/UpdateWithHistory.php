<?php

namespace Agregator\Order\Traits;

use Agregator\Order\History;
use T50ArrayHelper;
use T50DB;

trait UpdateWithHistory
{

	protected function updateWithHistory(int $orderId, int $id, array $arCodeValue, array $current, array $systemChanges = []){
		$ormClass = $this->getOrmClass(true);
		$history = new History;
		$history->init($orderId, $id);

		if( $current["ID"] <= 0 )
			return false;

		foreach($arCodeValue as $code => $value){
			$byManager = !in_array($code, $systemChanges);
		    $oldValue = $current[$code];
			if( $code == "UF_FLAGS" ){
				$value = $this->getNewFlags($ormClass, $oldValue, $value);
				$history->addChangesWithFlags($ormClass, $oldValue, $value, $byManager);
				$arCodeValue[$code] = $value;
			} else {
				$history->addChanges($ormClass::getTableName(), $code, array($oldValue, $value), $byManager);
			}
		}


		T50DB::startTransaction();
		$success = $ormClass::clas()::update($current["ID"], $arCodeValue)->isSuccess();

		if( $success && $history->save() ){
    		return T50DB::commit();
		}

    	return T50DB::rollback();
	}

	function getNewFlags($ormClass, $currentFlags, $flagCodeValue){
		if( empty($currentFlags) )
			$currentFlags = [];

		$flagCode = key($flagCodeValue);
		$switchOn = current($flagCodeValue);

		$flagId = $ormClass::getEnum("UF_FLAGS", false)[$flagCode]["id"];
		$newFlags = $currentFlags;
		$on = in_array($flagId, $newFlags);
		if( $switchOn == $on )
			return $newFlags;

		if( $switchOn )
			$newFlags[] = $flagId;
		else
			$newFlags = T50ArrayHelper::remItem($newFlags, $flagId);

		return $newFlags;
	}
}