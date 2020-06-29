<?php

namespace ORM;

use \Bitrix\Main\Application;

class DBQuery
{
	private $connection;
	private $sqlHelper;
	static $INSTANCE;

	private function __construct(){
		$this->connection = Application::getConnection();
        $this->sqlHelper = $this->connection->getSqlHelper();
	}

	static function getInstance(){
		if( self::$INSTANCE == null )
			self::$INSTANCE = new DBQuery();

		return self::$INSTANCE;
	}

	function multipleDeleteById($tableName, $ids = array()){
		$ids = array_map("intval", $ids);
		if( empty($ids) || !\T50ArrayHelper::isInt($ids) )
			return false;

		$where = "ID IN (" . implode(",", $ids) . ")";
		$sql = "DELETE FROM " . $tableName . " WHERE " . $where;
		$this->connection->queryExecute($sql);
		return $this->connection->getAffectedRowsCount();
	}
}
