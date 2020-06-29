<?php

class T50DB
{
	private static $transactNestingLevel = 0;

	public static function startTransaction(){
		if( ++self::$transactNestingLevel !== 1 )
			return;

		if( TESTS_RUNNING !== true )
			$GLOBALS["DB"]->StartTransaction();
	}

	public static function commit(){
		if( self::$transactNestingLevel -- !== 1 )
			return true;

		if( TESTS_RUNNING !== true )
			$GLOBALS["DB"]->Commit();

		return true;
	}

	public static function rollback(){
		if( self::$transactNestingLevel -- !== 1 )
			return false;

		if( TESTS_RUNNING !== true )
			$GLOBALS["DB"]->Rollback();

		return false;
	}
}
?>
