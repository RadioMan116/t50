<?php

namespace Agregator\Order\Traits;

use Agregator\Order\History;
use T50ArrayHelper;
use T50DB;
use Agregator\Manager\Manager;

trait SupplierCommission
{
	function setSupplierCommission(int $commission){
		if( !Manager::canChangeSuppliersData() )
			return false;

		if( $commission <= 0 )
			return false;

		return $this->update(["UF_COM_SUPPLIER" => $commission]);
	}
}