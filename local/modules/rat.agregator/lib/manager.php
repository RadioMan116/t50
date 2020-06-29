<?php

namespace rat\agregator;

use Bitrix\Main\Entity;

class ManagerTable extends \Bitrix\Main\UserTable
{
	public static function getMap()
	{
		return array(
			new Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true
            )),
			new Entity\StringField('LOGIN'),
			new Entity\StringField('ACTIVE'),
			new Entity\StringField('NAME'),
			new Entity\StringField('LAST_NAME'),
		);
	}
}