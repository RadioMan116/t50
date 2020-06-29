<?php

namespace Agregator;

abstract class CommonLogger
{
	private $counter = 0;

	protected $file;

	function __construct(){
		// init folder
		$folderName = trim($this->getFolderName(), "/");
		$dir = \T50FileSystem::initDir("/.logs/{$folderName}", "CommonLogger cannot create dir {$folderName}");

		// init filename
		$fileName = $this->getFileName();
		$fileName .= "_" . date("Y-m-d");

		// open file
		$filePath = $dir . "/{$fileName}.log";
		$this->file = fopen($filePath, "ab");

		// rotate
		$this->rotate($dir);
	}

	abstract protected function getFileName();
	abstract protected function getFolderName();
	abstract protected function rotateCount();

	function __destruct(){
		if( $this->file )
			fclose($this->file);
	}

	function log($message, $showTime = true){
		if( is_array($message) )
			$message = implode(PHP_EOL, $message);

		if( $showTime ){
			$time = date("H:i:s");
			$message = $time . PHP_EOL . $message;
		}

		$message .= PHP_EOL . PHP_EOL;

		fwrite($this->file, $message);

		$this->counter ++;
		if( $this->counter % $this->flushCount() == 0 )
			$this->flush();
	}

	private function flush(){
		$fileInfo = stream_get_meta_data($this->file);
		$filePath = $fileInfo["uri"];
		fclose($this->file);
		$this->file = fopen($filePath, "ab");
	}

	protected function flushCount(){
		return 20;
	}

	private function rotate($dir){
		$rotateCount = (int) $this->rotateCount();
		if( $rotateCount <= 0 )
			$rotateCount = 5;

		$files = array_filter(scandir($dir), function($file){
			return ( $file != "." && $file != ".." );
		});

		sort($files);

		$currentCount = count($files);
		$deleteFiles = array_slice($files, 0, $currentCount - $rotateCount);

		if( !empty($deleteFiles) ){
			foreach($deleteFiles as $file)
				@unlink($dir . "/" . $file);
		}
	}
}
?>