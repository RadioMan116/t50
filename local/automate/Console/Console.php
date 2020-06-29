<?php

namespace Console;

abstract class Console
{	
	private $attributes = array(
		"normal"        => 0,
		"bold"          => 1,
		"underline"     => 4,
		"blink"         => 5,
		"invert_colors" => 7,
		"invisible"     => 8,
	);
	
	private $colors = array(
		"black"      => 30,
		"red"        => 31,
		"green"      => 32,
		"yellow"     => 33,
		"blue"       => 34,
		"magenta"    => 35,
		"light_blue" => 36,
		"white"      => 37,
	);
	
	private $backgrounds = array(
		"black"      => 40,
		"red"        => 41,
		"green"      => 42,
		"yellow"     => 43,
		"blue"       => 44,
		"magenta"    => 45,
		"light_blue" => 46,
		"white"      => 47,
	);	
	
	private $buildedStyle = array();
	
	protected function getResetStyleCode(){
		static $code;
		if( $code != null )
			return $code;
		
		$this->setColor("white");
		$this->setBackground("black");
		$this->setAttribute("normal");		
		$code = $this->getCode();
		return $code;
	}
	
	protected function setColor($color){
		if( isset($this->colors[$color]) )
			$this->buildedStyle[] = $this->colors[$color];
			
		return $this;
	}
	
	protected function setBackground($bgColor){
		if( isset($this->backgrounds[$bgColor]) )
			$this->buildedStyle[] = $this->backgrounds[$bgColor];
			
		return $this;
	}
	
	protected function setAttribute(){
		$attributes = func_get_args();
		foreach($attributes as $attribute){
			if( isset($this->attributes[$attribute]) )
				$this->buildedStyle[] = $this->attributes[$attribute];
		}			
		return $this;
	}
	
	protected function getWrapContent($content){
		if( empty($this->buildedStyle) )
			return $content;			
		
		$styleCode = $this->getCode();
		$resetStyleCode = $this->getResetStyleCode();
		
		$lines = explode(PHP_EOL, $content);
		foreach($lines as $k => $line)
			$lines[$k] = $styleCode . $line;
		
		$content = implode(PHP_EOL, $lines) . $resetStyleCode;
		
		return $content;
	}
		
	private function getCode(){
		$code = implode(";", $this->buildedStyle);
		$code = "\x1b[{$code}m";
		$this->buildedStyle = array();		
		return $code;
	}
	
}

