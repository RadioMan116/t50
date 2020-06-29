<?php

namespace Console;

class Parser
{
	private static $INSTANCE;

	private function __construct(){}

	public static function parse($argv){
		if( self::$INSTANCE == null )
			self::$INSTANCE = new self;

		array_shift($argv);
		return self::$INSTANCE->_parse($argv);
	}

	private function _parse($argv){
		$input = new Input();
		$input->command = array_shift($argv);
		$input->class = $this->parseClass($input->command);
		$input->params = $this->parseParams($argv, ( $input->command == "help" ));
		$input->options = $this->parseOptions($argv);
		return $input;
	}

	private function parseParams($argv, $forHelp = false){
		$argvStr = " " . implode(" ", $argv);
		if( $forHelp ){
			if( preg_match("#[^-]([a-z:]+)#", $argvStr, $match) )
				return array("command" => $match[1]);

			return array();
		}
		$pattern = "#\s--([a-zA-Z]+[a-zA-Z0-9]{2,14})=([a-zA-Z0-9_~]+)#";
		$params = preg_match_all($pattern, $argvStr, $match);
		$params = array_combine($match[1], $match[2]);
		foreach($params as $code => $val){
			if( substr_count($val, "~") )
				$params[$code] = explode("~", $val);
		}
		return $params;
	}

	private function parseOptions($argv){
		$tmp = preg_grep("#^-[a-zA-Z]{1,10}$#", $argv);
		$options = array();
		foreach($tmp as $str){
			$str = substr($str, 1);
			$options = array_merge($options, str_split($str));
		}
		$options = array_unique($options);
		$options = array_fill_keys($options, true);
		return $options;
	}

	private function parseClass($code){
		$class = explode(":", $code);
		$class = array_map(function($part){
			return ucfirst($part);
		}, $class);
		$class = implode("", $class);
		$class = "\\Console\\Commands\\{$class}";
		return $class;
	}
}