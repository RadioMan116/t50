<?php
namespace Agregator\Common;

class ReportXml
{
	private $path;
	private $xmlDom;
	private $data;
	private $FLAG_FROMAT = true;
	private $attributes = array();

	function __construct(){
		$this->xmlDom = new \DOMDocument('1.0', 'windows-1251');
	}

	function setFilePath($path){
		$this->path = $path;
		return $this;
	}

	function setData(array $data = array()){
		$this->data = $data;
		unset($data);
		return $this;
	}

	function export(){
		if( $this->FLAG_FROMAT ){
			$this->xmlDom->preserveWhiteSpace = false;
			$this->xmlDom->formatOutput = true;
		}

		$this->write($this->data);

		foreach($this->attributes as $code => $val)
			$this->xmlDom->documentElement->setAttribute($code, $val);

		if( !empty($this->path) )
			return (bool) $this->xmlDom->save($this->path);

		return $this->xmlDom->saveXML();
	}

	function setAttribute($code, $value){
		if( is_scalar($code) && is_scalar($value) )
			$this->attributes[$code] = $value;
		return $this;
	}

	function setFormat($val){
		$this->FLAG_FROMAT = (bool) $val;
		return $this;
	}

	private function write($data, $rootNode = null){
		if( $rootNode == null ){
			$rootNode = $this->xmlDom->createElement("root");
			$this->xmlDom->appendChild($rootNode);
		}

		foreach($data as $code => $val){
			$code = ( is_int($code) ? "item" : $code );

			if( is_array($val) ){
				$node = $this->xmlDom->createElement($code);
				$this->write($val, $node);
				$rootNode->appendChild($node);
			} else {
				$node = $this->xmlDom->createElement($code, $val);
				$rootNode->appendChild($node);
			}
		}

		return $rootNode;
	}
}