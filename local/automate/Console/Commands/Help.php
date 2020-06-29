<?php

namespace Console\Commands;

use Console\Input;
use Console\CommandHandler;
use Console\Parser;

class Help extends CommandHandler
{
	protected $previewDescription = "Show this help";
	protected $detailDescription = <<<CODE
Show this help
optional -d show list with preview description
if indicate other command - show full detail info (e.q. help help)
CODE;

	public function execute(Input $input){
		if( !empty($input->params["command"]) ){
			$content = $this->outCommandFullInfo($input->params["command"]);
		} elseif ( $input->options["d"] ){
			$content = $this->outAllDetail();
		} else {
			$content = $this->outAllSimple();
		}
		return $this->write($content);
	}

	private function outAllSimple(){
		$commands = $this->getAllCommands();
		$commands = array_map(function($command){
			return "  <spanyellow>{$command}</spanyellow>";
		}, $commands);
		$content = "List of commands:" . PHP_EOL . implode(PHP_EOL, $commands);
		return $content;
	}

	private function outAllDetail(){
		$commands = $this->getCommandObject();
		foreach($commands as $command => $obj){
			$commands[$command] = "  <spanyellow>{$command}</spanyellow> - {$obj->getPreview()}";
		};
		$content = "List of commands:" . PHP_EOL . implode(PHP_EOL, $commands);
		return $content;
	}

	private function outCommandFullInfo($command){
		$obj = $this->getCommandObject($command);
		$preview = $obj->getPreview();
		$detail = $obj->getDetail();
		$content = "Command <spanyellow>{$command}</spanyellow>:" . PHP_EOL;
		$content .= $preview . PHP_EOL;
		$content .= $detail . PHP_EOL;
		return $content;
	}

	private function getAllCommands(){
		static $commands;
		if( $commands != null )
			return $commands;

		$commands = array();
		$dir = CLI_FOLDER_ROOT . "/Console/Commands";
		foreach(glob($dir . "/*.php") as $filename) {
			$className = basename($filename, ".php");
			$commands[] = $this->classToCode($className);
		}
		return $commands;
	}

	private function getCommandObject($com = null){
		$objects = array();
		$commands = $this->getAllCommands();

		if( !empty($com) ){
			if( !in_array($com, $commands) )
				return false;

			$commands = array($com);
		}

		foreach($commands as $command){
			if( $command == "help" ){
				$className = __CLASS__;
			} else {
				$input = Parser::parse(array("t50cli", $command));
				$className = $input->class;
			}

			$objects[$command] = new $className();
		}

		if( !empty($com) )
			return $objects[$com];

		return $objects;
	}

	private function classToCode($class){
		preg_match_all("#([A-Z][a-z]+)#", $class, $match);
		$parts = array_map("strtolower", $match[1]);
		$command = implode(":", $parts);
		return $command;
	}

}