<?php

namespace Console;

abstract class CommandHandler
{
	protected $previewDescription;
	protected $detailDescription;
	private $output;

	function __construct(){
		$this->output = new Output();
	}

	function getPreview(){
		return $this->previewDescription;
	}

	function getDetail(){
		return $this->detailDescription;
	}

	protected abstract function execute(Input $input);

	protected function __call($method, $arguments){
		if( !method_exists($this->output, $method) )
			$this->output->writeAndExit("<error>Undefined method \"{$method}\" for class Console\Output</error>");

		return call_user_func_array(array($this->output, $method), $arguments);
	}
}