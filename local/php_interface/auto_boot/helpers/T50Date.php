<?php

use Bitrix\Main\Type\DateTime as BxDateTime;
use Bitrix\Main\Type\Date as BxDate;

class T50Date
{
	function check($date, $format = ""){
		if( empty($format) )
			$format = T50GlobVars::get("DATE_FORMAT");

		$time = strtotime($date);
		return ( date($format, $time) == $date );
	}

	function convertDate($date, $fromFormat, $toFormat){
		if( empty($date) )
			return false;
		$time = strtotime($date);
		if( date($fromFormat, $time) != $date )
			return false;

		return date($toFormat, $time);
	}

	function compareDates($date1, $cond, $date2){
		$time1 = strtotime($date1);
		$time2 = strtotime($date2);
		if( $time1 === false || $time2 === false )
			return null;

		switch ($cond) {
			case "<":
				return $time1 < $time2;
			case "<=":
				return $time1 <= $time2;
			case "=":
			case "==":
				return $time1 == $time2;
			case ">=":
				return $time1 >= $time2;
			case ">":
				return $time1 > $time2;
			default:
				throw new \InvalidArgumentException("invalid condition \"{$code}\"");
		}
	}

	function bxdate(/*DateTime*/ $dateObj, $format = "d.m.Y"){
		if( !isset($dateObj) )
			return null;

		if( is_string($dateObj) )
			return $dateObj;

		return $dateObj->format($format);
	}

	function isDate($value){
		return (
			$value instanceof Bitrix\Main\Type\DateTime
			||
			$value instanceof Bitrix\Main\Type\Date
			||
			$value instanceof DateTime
		);
	}
}
?>