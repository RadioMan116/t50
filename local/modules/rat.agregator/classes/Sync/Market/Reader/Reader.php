<?php

namespace Agregator\Sync\Market\Reader;


abstract class Reader implements \Iterator
{
	private $path;

	private $reader;
	private $index;
	private $valid = true;
	private $dataItem;
	private $wrapNodeName;
	private $itemNodeName;

	function __construct($path, $wrapNodeName, $itemNodeName){
		if( empty($wrapNodeName) || empty($itemNodeName) )
			throw new \InvalidArgumentException('empty wrapNodeName or itemNodeName');
		$this->wrapNodeName = $wrapNodeName;
		$this->itemNodeName = $itemNodeName;
		$this->path = $path;
		$this->rewind();
	}

	abstract function parseNodeToArray($domNode);

	function current(){
		if( $this->index == -1 ){
			$this->parse();
		}
		return $this->parseNodeToArray($this->dataItem);
	}

	function key(){
		return $this->index;
	}

	function next(){
		$this->parse();
	}

	private function parse(){
		static $opened = false;
		while( $this->reader->read() ){
			if( $this->isOpen($this->wrapNodeName) )
				$opened = true;

			if( !$opened )
				continue;

			if( $this->isClose($this->wrapNodeName) ){
				$this->valid = false;
				break;
			}

			if( $this->isOpen($this->itemNodeName) ) {
				$simpleXml = simplexml_import_dom($this->reader->expand(new \DOMDocument()));
				$this->dataItem = $simpleXml;
				$this->index ++;
				break;
			}
		}
	}

	function rewind($filePath = null){
		$this->index = -1;
		$this->reader = new \XMLReader();
		$this->reader->open($this->path);
		$this->valid = true;
	}

	function valid(){
		return $this->valid;
	}

	private function isOpen($nodName){
		return ( $this->reader->nodeType == \XMLREADER::ELEMENT && $this->reader->localName == $nodName );
	}

	private function isClose($nodName){
		return ( $this->reader->nodeType == \XMLREADER::END_ELEMENT  && $this->reader->localName == $nodName );
	}
}