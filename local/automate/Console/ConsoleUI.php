<?php

namespace Console;

abstract class ConsoleUI extends Console
{
	protected $content;

	protected function compile($content){
		$this->content = $content;
		return $this->parseTags();
	}

	private function tagDesign($tag){
		switch($tag){
			case "info":
				$this->setBackground("white");
				$this->setColor("black");
			break;
			case "warning":
				$this->setBackground("yellow");
				$this->setColor("black");
			break;
			case "error":
				$this->setBackground("red");
				$this->setColor("black");
			break;
			case "success":
				$this->setBackground("green");
				$this->setColor("black");
			break;
			case "bold":
				$this->setBackground("yellow");
				$this->setColor("black");
				$this->setAttribute("blink");
			break;
			case "spanyellow":
				$this->setColor("yellow");
			break;
			case "spanred":
				$this->setColor("red");
			break;
			case "spanwhite":
				$this->setColor("white");
			break;
		}
	}

	private function parseTags(){
		$tagParser = new TagParser($this->content);
		$result = $tagParser->getResult();

		$content = $this->getWrapContent($result["MAIN"]);
		unset($result["MAIN"]);
		foreach($result as $code => $item){
			$this->tagDesign($item["tag"]);
			$replace = $this->getWrapContent($item["content"]);
			$content = str_replace("$[-{$code}-]$", $replace, $content);
		}
		$content = $this->addPadding($content);
		return $content;
	}

	protected function addPadding($content){
		$resetStyleCode = $this->getResetStyleCode();
		$lines = explode(PHP_EOL, $content);
		foreach($lines as $k => $line)
			$lines[$k] = $resetStyleCode . "  " . $line . $resetStyleCode;
		return implode(PHP_EOL, $lines);
	}

	function __get($prop){
		if( in_array($prop, array("mode", "content")) )
			return $this->$prop;
	}
}