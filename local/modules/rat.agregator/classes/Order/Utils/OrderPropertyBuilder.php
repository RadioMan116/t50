<?php

namespace Agregator\Order\Utils;

use Bitrix\Main\Entity;
use rat\agregator\OrderProperty;
use Bitrix\Main\DB;

class OrderPropertyBuilder
{
	const TYPE_INT = "TYPE_INT";
	const TYPE_STR = "TYPE_STR";
	const TYPE_DATE = "TYPE_DATE";

	private $fieldsType = array();
	private $filter = array();

	private $runtime = array();
	private $validFilter = array();

	function getRuntime(){
		return $this->runtime;
	}

	function getFilter(){
		return $this->validFilter;
	}

	function setFields(array $fields){
		foreach($fields as $field){
			list($type, $field) = $this->detectType($field);
			$this->fieldsType[$field] = $type;
		}
		return $this;
	}

	function setFilter(array $filter){
		foreach($filter as $field => $value){
		    list($type, $field) = $this->detectType($field);
		    list($condition, $validField) = $this->detectFilterCondition($field);
		    $this->filter[] = [$condition, $validField, $value];
		    $this->fieldsType[$validField] = $type;
		}
		return $this;
	}

	private function detectFilterCondition($field){
		preg_match("#([^\w_]+)?([\w_]+)#", $field, $match);
		return [$match[1], $match[2]];
	}

	private function detectType($field){
		static $typesMap = array(
			"str" => self::TYPE_STR,
			"string" => self::TYPE_STR,
			"int" => self::TYPE_INT,
			"date" => self::TYPE_DATE,
		);

	    list($type, $field) = explode(" ", trim($field));
	    $type = trim($type);
	    $field = trim($field);

	    if( empty($field) ){
	    	$field = $type;
	    	$type = "string";
	    }

	    if( !isset($typesMap[$type]) )
	    	throw new \RuntimeException("invalid type {$type}");

		return [$typesMap[$type], $field];
	}

	function build(){
		$this->runtime = $this->validFilter = array();
		$class = OrderProperty::clas();
		$propdsCodeId = array_flip(OrderProperty::getEnum("UF_PROPERTY"));

		foreach($this->fieldsType as $field => $type){
		    $type = "UF_STRING";
			if( $this->fieldsType[$field] == self::TYPE_INT )
				$type = "UF_NUMBER";

			$prop = "PROP__{$field}";

			$this->runtime[] = new Entity\ReferenceField($prop, $class, [
				'=this.ID' => 'ref.UF_ORDER_ID',
				'=ref.UF_PROPERTY' => new DB\SqlExpression('?i', $propdsCodeId[$field])
			]);


			if( $this->fieldsType[$field] == self::TYPE_DATE ){
				$this->runtime[] = new Entity\ExpressionField(
					"{$field}__AS_DATE",
					"str_to_date(%s, '%%d.%%m.%%Y')",
					array("{$prop}.{$type}")
				);
			}

			$this->runtime[] = new Entity\ExpressionField($field, "`order_{$prop}`.`{$type}`");
		}

		foreach($this->filter as $item){
			list($condition, $field, $value) = $item;
			if( !isset($propdsCodeId[$field]) )
				throw new \RuntimeException("unknown field {$field}");

			$prop = "PROP__{$field}";

			$type = "UF_STRING";
			if( $this->fieldsType[$field] == self::TYPE_INT )
				$type = "UF_NUMBER";

			if( $this->fieldsType[$field] == self::TYPE_DATE ){
				$value = new DB\SqlExpression('str_to_date(?s, "%d.%m.%Y")', $value);
				$this->validFilter["{$condition}{$field}__AS_DATE"] = $value;
			} else {
				$this->validFilter["{$condition}{$prop}.{$type}"] = $value;
			}

		}

		return $this;
	}

}