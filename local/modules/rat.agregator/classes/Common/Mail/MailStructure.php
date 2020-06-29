<?php

namespace Agregator\Common\Mail;


class MailStructure
{
	private $data = [];

	function __set($code, $value){
		$validProps = [
			"from",
			"to",
			"subject",
			"body",
			"file",
			"priority",
			"reply_to",
			"cc",
			"bcc",
			"content_type"
		];
		if( !in_array($code, $validProps) )
			return;

		$this->data[$code] = $value;
	}

	function __get($code){
		$value = $this->data[$code];

		if( $code == "body" && empty($value))
			return ".";

		return $value;
	}

	function getData(){
		return $this->data;
	}
}