<?php

namespace Agregator\Order\Traits;


trait Flags
{
	function setFlag($flagCode, bool $switchOn){
		if( !method_exists($this, "getOrmClass") || !method_exists($this, "update") )
			return false;

		$flags = $this->getOrmClass(true)::getEnum("UF_FLAGS", false);
		if( !isset($flags[$flagCode]) )
			return false;

		return $this->update(["UF_FLAGS" => [$flagCode => $switchOn]]);
	}
}