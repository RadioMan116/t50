<?php

namespace Agregator\Components;

use Bitrix\Main\Context;
use Agregator\Components\Validation\Validator;

abstract class BaseComponent extends \CBitrixComponent
{
	use Traits\ComponentData;

	protected $input;
	protected $validator;

	function __construct($component = null){
		parent::__construct($component);
		$this->input = new \StdClass;
		$this->validator = new Validator;

		if( isset(class_uses(static::class)[Traits\ComponentDI::class]) )
			static::registerAutoload();
	}
}
