<?php
namespace Agregator\Sync\Catalog;

class ValidatorReport extends Reporter
{
	const ERR_MIX_MODEL = "MIX_MODEL";
	const ERR_EMPT_MODEL = "EMPT_MODEL";
	const ERR_SECTION = "ESECTION";
	const ERR_BRAND = "EBRAND";
	const ERR_CHANGE_MODEL = "CH_MODEL";
	const ERR_MOD_DOUBLES = "MDOUBLES";
	const ERR_UNID_DOUBLES = "UDOUBLES";

	const REPORT_T50 = "t50";
	const REPORT_CONTENT = "content";

	function makeReport(){
		$paths = $this->getPaths();

		$headersForMe = array(
			self::ERR_SECTION => "Нет раздела в t50",
			self::ERR_BRAND => "Нет бренда в t50",
			self::ERR_CHANGE_MODEL => "Была изменена модель",
		);
		$this->saveReportExcel($headersForMe, $paths[self::REPORT_T50]);
		$this->sendEmail(
			\T50Config::get("reports_emails")["admin"],
			"Проблемы синхрона c сайтом " . $this->shop["NAME"] . " (" . $this->shop["PROPERTY_HTTP_HOST_VALUE"] . ")",
			$paths[self::REPORT_T50]
		);

		$headersForContent = array(
			self::ERR_MIX_MODEL => "Смешанная модель",
			self::ERR_EMPT_MODEL => "Пустая модель",
			self::ERR_CHANGE_MODEL => "Была изменена модель",
			self::ERR_MOD_DOUBLES => "Дубли моделей",
			self::ERR_UNID_DOUBLES => "Дубли юнидов",
		);
		$this->saveReportExcel($headersForContent, $paths[self::REPORT_CONTENT]);
		$this->sendEmail(
			\T50Config::get("reports_emails")["SYNC_CATALOG_VALIDATOR"],
			"Проблемы синхрона c сайтом " . $this->shop["NAME"] . " (" . $this->shop["PROPERTY_HTTP_HOST_VALUE"] . ")",
			$paths[self::REPORT_CONTENT]
		);

		return $this;
	}

	function hasErrors(){
		return !empty($this->data);
	}

	private function getPaths(){
		$dir = \T50FileSystem::initDir("/.logs/sync_files/catalog_reports");
		$shopCode = $this->shop["CODE"];
		$paths = [];
		foreach(array(self::REPORT_T50, self::REPORT_CONTENT) as $type){
			$paths[$type] = "{$dir}/{$shopCode}_{$type}.xlsx";
			@unlink($paths[$type]);
		}
		return $paths;
	}
}