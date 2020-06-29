<?php
namespace Agregator\IB;

use \Bitrix\Main\Loader;

class ElementProps
{
	private static $INSTANCE;
	private $id;
	private $iblockId;
	private $code;
	private $value;
	private $description;
	private $currentProps;	
	
	public static function listAdd($id, $iblockId, $code, $val, $description = null){
		self::init(func_get_args());
		if( self::$INSTANCE->existInList() )
			return true;
			
		return self::$INSTANCE->update(true);
	}	
	
	public static function listRemove($id, $iblockId, $code, $val){
		self::init(func_get_args());
		if( !self::$INSTANCE->existInList() )
			return true;		
		
		return self::$INSTANCE->update(false);
	}
	
	private function update($add){		
		$newData = array();
		if( $add )
			$newData = array(array("VALUE" => $this->value, "DESCRIPTION" => $this->description));
		
		foreach($this->currentProps as $item){			
			if( (string)$item["VALUE"] == (string)$this->value )
				continue;
			
			$newData[] = array(
				"VALUE" => $item["VALUE"],
				"DESCRIPTION" => $item["DESCRIPTION"]
			);
		}
		
		if( empty($newData) )
			$newData = false;
		
		\CIBlockElement::SetPropertyValuesEx($this->id, $this->iblockId, 
			array($this->code => $newData)
		);
		return true;
	}
	
	private function existInList(){
		$val = $this->value;
		$description = $this->description;
		$exists = array_filter($this->currentProps, function($item) use($val, $description){
			$matchedDescription = true;
			if( $description !== null )
				$matchedDescription = ( $item["DESCRIPTION"] == $description );			
			return (  $item["VALUE"] == $val && $matchedDescription );
		});
		return !empty($exists);
	}
	
	public static function listGet($id, $iblockId, $code, $full = false){
		$arResult = array();
		$res = \CIBlockElement::GetProperty($iblockId, $id, "sort", "asc", array("CODE" => $code));		
		while( $result = $res->Fetch() )			
			$arResult[] = ( $full ? $result : $result["VALUE"] );
		
		return $arResult;
	}
	
	public static function init($args){
		if( self::$INSTANCE == null )
			self::$INSTANCE = new self;
		
		self::$INSTANCE->initArgs($args);
	}
	
	private function initArgs($args){
		$this->id = (int) $args[0];
		$this->iblockId = (int) $args[1];			
		$this->code = htmlspecialchars($args[2]);
		$this->value = $args[3];
		$this->description = $args[4];
		if( !is_scalar($this->description) && $this->description != null )
			$this->description = "";
		
		if( $this->id <= 0 || $this->iblockId <= 0 || empty($this->code) || !is_scalar($this->value) )
			throw new \InvalidArgumentException("Invalid arguments ElementProps");
		
		$this->currentProps = self::listGet($this->id, $this->iblockId, $this->code, true);
		
		return $this;
	}
	
	
}