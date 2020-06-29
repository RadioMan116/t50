<?php

namespace Agregator\Order\Docs;

use PhpOffice\PhpWord\TemplateProcessor;


class Word extends Generator
{
	function init(string $path){
		$this->handler = new TemplateProcessor($path);
	}

	function download(string $name){
		$tmpFileHandler = tmpfile();
		$tmpFilePath = stream_get_meta_data($tmpFileHandler)['uri'];
		$this->handler->saveAs($tmpFilePath);
		$GLOBALS['APPLICATION']->RestartBuffer();
		header("Content-Disposition: attachment; filename=" . $name);
		header("Pragma: no-cache");
		header("Expires: 0");
		readfile($tmpFilePath);
		fclose($tmpFileHandler);
		die();
	}

	function setData(array $data){
		foreach($data as $code => $item){
		    if( is_scalar($item) ){
		    	$this->handler->setValue($code, $item);
		    } else {
		    	$this->handler->setComplexBlock($code, $item);
		    }
		}
	}
}
