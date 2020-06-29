<?php

$mapReportsEmails = array(
	"SYNC_CATALOG_VALIDATOR" => [/*"conent@ret-team.ru",*/ "e.demidov@ret-team.ru"],
	"admin" => "e.demidov@ret-team.ru",
);

if( ENV != "PRODUCTION" ){
	$mapReportsEmails = array_map(function ($email){
		return "e.demidov@ret-team.ru";
	}, $mapReportsEmails);
}

return $mapReportsEmails;