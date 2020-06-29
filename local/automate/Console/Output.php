<?php

namespace Console;

class Output extends ConsoleUI
{
	private $stdout;

	function __construct(){
		$this->stdout = fopen("php://stdout", "w");
	}

	function __destruct(){
		@fclose($this->stdout);
	}

	function write($content, $addEol = true){
		$eol = ( $addEol ? PHP_EOL : "" );
		fwrite($this->stdout, $this->compile($content) . $eol);
	}

	function writeAndExit($content){
		$this->write($content);
		exit();
	}

	function select($message, array $vars, $addVarsToMessage = true){
		static $attempts = 0;
		if( $addVarsToMessage )
			$message .= " (" . implode("|", $vars) . "):";

		$this->write($message, false);

		$answer = trim(stream_get_line(STDIN, 128, PHP_EOL));

		if( !in_array($answer, $vars) ){
			if( ++ $attempts > 5 )
				$this->writeAndExit("<error>more than 5 wrong attempts</error>");

			return $this->select($message, $vars, false);
		}

		$attempts = 0;

		return $answer;
	}

	function confirm($message){
		$message .= " (Y/N):";
		$answer = $this->select($message, ["Y", "N"], false);
		return ( $answer == "Y" );
	}
}