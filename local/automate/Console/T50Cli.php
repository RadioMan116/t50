<?php

namespace Console;

class T50Cli
{
	public static function run($argv){
		$input = Parser::parse($argv);
		$className = $input->class;

		if( !class_exists($className) ){
			$message = "<error>Not found command \"{$input->command}\"</error>";
			(new Output())->write($message);
			self::run(array("", "help"));
			die();
		}

		$executor = new $className();
		$output = $executor->execute($input);
		if( $output )
			$output->render();
	}
}