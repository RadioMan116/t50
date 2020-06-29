<?php

namespace rat\agregator;

use Bitrix\Main\Entity;

class HiddenSupplier extends \ORM\BaseDataManager
{
	public static function getTableName(){
		return 't50_hidden_suppliers';
	}
}