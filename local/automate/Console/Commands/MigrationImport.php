<?php

namespace Console\Commands;

use Console\Input;
use Console\CommandHandler;
use Bitrix\Main\Loader;
use ORM\Migration\Import;
use ORM\Migration\Updater;


class MigrationImport extends CommandHandler
{
	protected $previewDescription = "import orm";
	protected $detailDescription = <<<CODE
Import orm from xml.
Interactive choice of exists orms or specify orm in variable --orm
xml file must be placed in a directory /upload/ with name equals entity name
CODE;

	public function execute(Input $input, $forceUpdate = false){
		Loader::includeModule('highloadblock');
		$validOrms = array_column(\ORM\ORMInfo::getHLTables(), "NAME");
		$importOrm = $input->params["orm"];
		if( !in_array($importOrm, $validOrms) )
			$importOrm = $this->select("Choose orm", $validOrms);

		$import = new Import();
		$import->setOrm($importOrm);
		if( !$import->exec() ){
			$this->printErrors($import, "orm \"{$importOrm}\" import errors");
			die();
		}

		if( $forceUpdate ){
			$updater = new Updater($import);
			$updater->exec();
			if( $updater->hasErrors() ){
				$errors = implode("; ", $updater->getErrors());
				$this->writeAndExit("<error>Update errors: {$text}:<bold> {$errors}</bold></error>");
			} else {
				$this->writeAndExit("<success>success update</success>");
			}
		} else {
			$this->printErrors($import);
			$this->printPreview($import->getPreview());
			$this->write(PHP_EOL);
			if( $import->needUpdate() && $this->confirm("Continue?") ){
				$input->params = ["orm" => $importOrm];
				$this->execute($input, true);
				$this->write(PHP_EOL);
			}
		}
	}

	private function printErrors(Import $import, $text = ""){
		if( !$import->hasErrors() )
			return;

		if( empty($text) )
			$text = "errors";

		$errors = implode("; ", $import->getErrors());
		$this->write("<error>{$text}:<bold> {$errors}</bold></error>");
	}

	private function printPreview($preview){
		foreach($preview as $block){
			$empty = ( empty($block['items']) ? "<success> up to date</success>" : "" );
			$this->write("<info>{$block['title']}:{$empty}</info>");
			foreach($block['items'] as $item){
				if( !is_array($item) ){
					$this->write("    {$item}");
					continue;
				}

				foreach($item as $enum){
					$this->write("    <info>{$enum['title']}:</info>");
					foreach($enum['items'] as $row){
						$this->write("        {$row}");
					}
				}
			}
		}
	}
}