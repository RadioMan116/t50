<?php

namespace Console;

class TagParser
{	
	private $data;
	private $content;
	private $tagsName = array();
	
	function __construct($content){
		$this->content = $content;
		$this->prepare();
		$this->parse();
	}
	
	private function parse(){		
		preg_match_all("#<(?:([a-z]+)|/([a-z]+))>#s", $this->content, $matches, PREG_OFFSET_CAPTURE);		
		$countTags = count($matches[0]);
		
		$closeTag = "";
		for($i = $countTags - 1; $i >= 0; $i --){
			if( substr_count($matches[0][$i][0], "/") == 0 ){
				$indexStart = $i;
				$closeTag = "</{$matches[1][$i][0]}>";
				break;
			}
		}
		if( $indexStart === null ){
			$this->data["MAIN"] = $this->content;
			$this->content = "";
			return;
		}
			
		for($i = $indexStart; $i < $countTags; $i ++){
			if( $matches[0][$i][0] == $closeTag ){
				$indexEnd = $i;
				break;
			}
		}
		
		$tagName = $this->getTagName($matches[1][$indexStart][0]);
		
		$start = $matches[0][$indexStart][1] + strlen($matches[0][$indexStart][0]);
		$length = $matches[0][$indexEnd][1] - $start;
		$content = substr($this->content, $start, $length);	
		$this->data[$tagName] = $content;		
		$startCut = $matches[0][$indexStart][1];
		$lengthCut = $matches[0][$indexEnd][1] + strlen($matches[0][$indexEnd][0]) - $startCut;
		$this->content = substr_replace($this->content, "$[-{$tagName}-]$", $startCut, $lengthCut );
		$this->parse();
	}
	
	private function prepare(){
		// clear empty tags
		$this->content = preg_replace("#<([^>]+)></\\1>#", "", $this->content);
		preg_match_all("#<(?:([a-z]+)|/([a-z]+))>#", $this->content, $matches);
		$filter = function($value){return !empty($value);};
		$opens = array_filter($matches[1], $filter);
		$closes = array_filter($matches[2], $filter);
		if( count($opens) != count($closes) )
			$this->content = "";
	}
	
	private function getTagName($tag){		
		if( !isset($this->tagsName[$tag]) )
			$this->tagsName[$tag] = 0;
		
		$this->tagsName[$tag] ++;		
		return "{$tag}_{$this->tagsName[$tag]}";
	}

	function getResult(){
		foreach($this->data as $code => $content){
			if( $code == "MAIN" )
				break;
			
			$tag = preg_replace("#[^a-z]#", "", $code);			
			$this->data[$code] = compact("content", "tag");
		}
		$this->data = array_reverse($this->data);
		return $this->data;
	}
}