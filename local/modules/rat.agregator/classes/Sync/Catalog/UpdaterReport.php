<?php
namespace Agregator\Sync\Catalog;

class UpdaterReport extends Reporter
{
	const LOG_NEW = "NEW";
	const LOG_UPDATE = "UPDATE";
	const LOG_FAIL = "FAIL";

	function makeReport(){
		$file = $this->getPath();

		$headersForMe = array(
			self::LOG_NEW => "Новые",
			self::LOG_UPDATE => "Обновлено",
			self::LOG_FAIL => "Ошибки",
		);
		$this->saveReportExcel($headersForMe, $file);

		if( $this->hasErrors() ){
			$this->sendEmail(
				\T50Config::get("reports_emails")["admin"],
				"Ошибки обновления сайта " . $this->shop["NAME"] . " (" . $this->shop["PROPERTY_HTTP_HOST_VALUE"] . ")",
				$file
			);
		}
	}

	function hasErrors(){
		return !empty($this->data[self::LOG_FAIL]);
	}

	private function getPath(){
		$dir = \T50FileSystem::initDir("/.logs/sync_files/catalog_reports");
		$file = $dir . "/" . $this->shop["CODE"] . "_update";
		@unlink($file);
		return $file;
	}
}