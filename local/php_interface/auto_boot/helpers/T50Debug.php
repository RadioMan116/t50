<?php

use Bitrix\Main\Diag\SqlTracker;
use Agregator\Common\Mail;
use Agregator\Logger;

class T50Debug
{
	function dumpTracker(SqlTracker $tracker){
		echo ( IS_CLI === true ? "" : "<pre>" );
		foreach ($tracker->getQueries() as $query) {
			$sql = $query->getSql();
			$time = $query->getTime();
			$stackTrace = $query->getTrace();
			$stackTraceLines = array_map(function ($item){
				return "{$item['file']} [{$item['line']}]";
			}, $stackTrace);

			echo "--------------" . PHP_EOL;

			echo "Time: {$time}" . PHP_EOL;

			echo $sql . PHP_EOL;

			echo PHP_EOL;
			foreach($stackTraceLines as $stackTraceLine)
				echo $stackTraceLine . PHP_EOL;

			echo "--------------" . PHP_EOL . PHP_EOL;
		}
		echo ( IS_CLI === true ? "" : "</pre>" );
	}

	static function isWin(){
		return (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
	}

	static function showBacktrace(){
		echo ( IS_CLI === true ? "" : "<pre style='margin:30px;'>" );
		echo "------------------" . PHP_EOL;
		foreach(debug_backtrace() as $k => $item) {
			if( $k == 0 )
				continue;

			echo $item["file"] . " [" . $item["line"] . "]" . PHP_EOL;

			if( isset($item["class"]) )
				echo $item["class"] . $item["type"];
			echo $item["function"] . "()" . PHP_EOL;

			echo PHP_EOL;
		}
		echo "------------------" . PHP_EOL . PHP_EOL;
		echo ( IS_CLI === true ? "" : "</pre>" );
	}

	static function alert($message){
		$logger = new Logger("Alert");
		$logger->log($message);
		Mail::send("", T50Config::get("reports_emails.admin"), "Alert!!", $message);
	}

	static function time($open){
		static $microtime;
		( $open ? $microtime = -microtime(true) : $microtime += microtime(true) );

		$min = date("i", $microtime);
		$sec = date("s", $microtime);
		$mc = strstr($microtime, ".");
		return "[execution time: {$min}:{$sec}{$mc}]";
	}

	static function log($message){
		static $logger;
		$logger = $logger ?? new Agregator\Logger("debug");
		$logger->log($message, false);
	}

	static function cache($value, $reset = false, $name = ""){
		$name = substr(md5($name . date("d.m.Y")), 0, 7);
		$path = $_SERVER["DOCUMENT_ROOT"] . "/.logs/TEMP/{$name}.tmp";
		if( file_exists($path) && !$reset )
			return include $path;

		$export = var_export($value, true);
		$content = "<?php\n\nreturn {$export};";
		return file_put_contents($path, $content);
	}
}