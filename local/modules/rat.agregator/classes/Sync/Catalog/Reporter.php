<?php
namespace Agregator\Sync\Catalog;

use Agregator\Common\ReportExcel;
use Agregator\Common\Mail;

abstract class Reporter extends Base
{
	protected $data = array();

	abstract function makeReport();
	abstract function hasErrors();

	function add($code, $data){
		if( !isset($this->data[$code]) )
			$this->data[$code] = array();

		$this->data[$code][] = $data;
	}

	protected function saveReportExcel($titles, $path){
		$reportExcel = new ReportExcel();
		$headers = array("id", "model", "name", "url");

		foreach($titles as $code => $title){
			$rows = array();
			foreach($this->data[$code] as $item){
				$row = array();
				foreach($headers as $field){
					$row[] = $item[$field];
				}
				$rows[] = $row;
			}
			if( !empty($rows) )
				$reportExcel->createList($title, $rows, $headers);
		}

		if( $reportExcel->isEmpty() )
			return;

		if( !$reportExcel->saveFile($path) )
			$this->logger->log("cannot write data to {$path}");
	}

	protected function sendEmail($to, $message, $file){
		if( !file_exists($file) )
			return;

		$mailFields = array(
			"TO_EMAILS"	=>	$to,
			"THEMA" => $message,
			"BODY" => $message . PHP_EOL . "Отчет в excel.",
		);
		// \CEvent::Send("UNIVERSAL", "s1", $mailFields, "", "", array($file));
		Mail::send("", $to, $message, $message . PHP_EOL . "Отчет в excel.", $file);
	}

}