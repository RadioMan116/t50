<?php

namespace Console\Commands;

use Console\Input;
use Console\CommandHandler;
use Bitrix\Main\Loader;
use ORM\Migration\Export;


class MigrationExport extends CommandHandler
{
	protected $previewDescription = "export orm";
	protected $detailDescription = <<<CODE
Export orm as xml in upload folder.
Interactive choice of exists orms or specify orm in variable --orm
CODE;

	public function execute(Input $input){
		Loader::includeModule('highloadblock');
		$validOrms = array_column(\ORM\ORMInfo::getHLTables(), "NAME");
		$exportOrm = $input->params["orm"];
		if( !in_array($exportOrm, $validOrms) )
			$exportOrm = $this->select("Choose orm", $validOrms);

		$export = new Export();
		$export->setOrm($exportOrm);
		if( $export->exec() )
			$this->writeAndExit("<success>orm \"{$exportOrm}\" success exported</success>");

		$errors = implode("; ", $export->getErrors());
		$this->writeAndExit("<error>orm \"{$exportOrm}\" export errors:<bold> {$errors}</bold></error>");
	}
}