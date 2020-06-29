<?php

namespace Agregator\Order;


abstract class OrderBase
{
	use Traits\Log;
	use Traits\UpdateWithHistory;

	protected function getOrmClass($inner = false){
		$currentClass = get_class($this);
		$libClass = str_replace("Agregator\\Order\\", "rat\\agregator\\", $currentClass);
		if( $inner )
			return $libClass;

		return $libClass::clas();
	}
}