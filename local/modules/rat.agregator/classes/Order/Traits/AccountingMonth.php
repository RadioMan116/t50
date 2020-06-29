<?php

namespace Agregator\Order\Traits;

use Agregator\Order\History;
use Agregator\Manager\Manager;
use T50ArrayHelper;
use T50DB;

trait AccountingMonth
{
	function setMonthZP(int $month){
		return $this->checkAndUpdateMonth("UF_MONTH_ACC_ZP", $month);
	}

	function setMonthVP(int $month){
		return $this->checkAndUpdateMonth("UF_MONTH_ACC_VP", $month);
	}

	private function checkAndUpdateMonth($code, $month){
		if( !in_array($month, range(1, 12)) ){
			if( class_uses(Log::class) )
				return $this->logError("not valid month for {$code}: " . var_export($month, true));

			return false;
		}

		if( !Manager::canChangeAccountingMonth() )
			return false;

		return $this->update([$code => $month]);
	}
}