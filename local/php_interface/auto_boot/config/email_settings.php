<?php

$data = [
	"info@t50.su" => [
		"imap" => ["mail.ru", "md5>JrFb1", "t50mail@ret-team.ru"],
		"smtp" => ["yandex.ru", "OnxFc66aN9", "info@t50.su"],
	],
	"shop@schaub-lorenz-russia.ru" =>  ["mail.ru", "08^xFFgvjlRQ"],
	"shop@ag-russia.ru" =>  ["yandex.ru", "biRznG7zk5DY"],
	"info@vf-shop.ru" =>  ["mail.ru", "r|4OAWXjlp7d"],
	"shop@dewalt.su" =>  ["yandex.ru", "biRznG7zk5DY"],
	"shop@shop-makita.ru" =>  ["yandex.ru", "g5auR#gNRtYk"],
	"shop@sim-studio.ru" =>  ["yandex.ru", "biRznG7zk5DY"],
	"sale@elica-home.ru" =>  ["mail.ru", "fX6d~I4kksOI"],
	"sale@gorenje-home.ru" =>  ["mail.ru", "g|18yVQCYtor"],
	"shop@gorenje-ru.ru" =>  ["yandex.ru", "biRznG7zk5DY"],
	"shop@schock-ru.ru" =>  ["yandex.ru", "biRznG7zk5DY"],
	"sale@falmec-home.ru" =>  ["mail.ru", "1YY2S*yzFurx"],
	"shop@l-rus.ru" =>  ["yandex.ru", "biRznG7zk5DY"],
	"shop@teka-ru.ru" =>  ["yandex.ru", "biRznG7zk5DY"],
	"order@blanco-home.ru" =>  ["mail.ru", "V5d@1yhOC_njl"],
	"sale@kuppersberg-shop24.ru" =>  ["mail.ru", "l#DM8zPEf8vv"],
	"order@sm-russia.ru" =>  ["mail.ru", "2NN2QjFxy;wr"],
	"shop@nf-rus.ru" =>  ["yandex.ru", "biRznG7zk5DY"],
	"order@asko-shop24.ru" =>  ["mail.ru", "DfnuD4+8XYkn"],
	"shop@redverg-shop.ru" =>  ["yandex.ru", "biRznG7zk5DY"],
	"shop@sm-rus.ru" =>  ["yandex.ru", "biRznG7zk5DY"],
	"shop@bs-rus.ru" =>  ["yandex.ru", "biRznG7zk5DY"],
	"sale@graude-store.ru" =>  ["mail.ru", 'vhPNs$3K5zxPQ'],
	"shop@kuppers.ru" =>  ["yandex.ru", "biRznG7zk5DY"],
	"shop@blanco-markt.ru" =>  ["yandex.ru", "biRznG7zk5DY"],
	"shop@asko-russia.ru" =>  ["yandex.ru", "biRznG7zk5DY"],
	"shop@kaiser-studio.ru" =>  ["yandex.ru", "biRznG7zk5DY"],
	"shop@elux-ru.ru" =>  ["yandex.ru", "biRznG7zk5DY"],
	"shop@hansa-ru.ru" =>  ["yandex.ru", "biRznG7zk5DY"],
	"shop@korting-dealer.ru" =>  ["yandex.ru", "biRznG7zk5DY"],
	"shop@miele-rus.ru" =>  ["gmail.com", "gQ*nD=4Y"],
	"shop@mitsubishi-japan.ru" =>  ["mail.ru", "pSHgIe1}pJ2a"],
	"shop@weissgauff-russia.ru" =>  ["yandex.ru", "biRznG7zk5DY"],
	"order@nf-store.ru" =>  ["mail.ru", "TE[68uiUmKug"],
	"shop@faber-studio.ru" =>  ["yandex.ru", "biRznG7zk5DY"],
	"shop@rosenlew-holodilnik.ru" =>  ["yandex.ru", "biRznG7zk5DY"],
	"shop@hitachi-holodilnik.ru" =>  ["yandex.ru", "biRznG7zk5DY"],
	"sale@franke-russia.ru" =>  ["mail.ru", "@Hag6nd3bKXJ"],
	"shop@dedietrich-studio.ru" =>  ["yandex.ru", "biRznG7zk5DY"],
	"sale@insinkerator-shop.ru" =>  ["mail.ru", "biRznG7zk5DY"],
	"order@candy-rus.ru" =>  ["yandex.ru", "Hg8#z4avN_8yvl"],
	"order@beko-hwtrade.ru" =>  ["mail.ru", "Q0p#f1a7f_eIB"],
	"order@whirlpool-online.ru" =>  ["mail.ru", "LKi_GxQA4@NIs"],
	"shop@vezemmebel.ru" =>  ["yandex.ru", "Mc-ERXrv4bf4"],
	"shop@technopride.ru" =>  ["yandex.ru", "OReF_s5H3Im"],
	"sale@jetair-home.ru" =>  ["mail.ru", "W`0Cj6xgKzFo"],
	"shop@maunfeld-home.ru" =>  ["mail.ru", "fHW6r0@3mxOAMr"],
	"shop@hausdorf.ru" =>  ["gmail.com", "9h>MM<H>"],
	"partners@belampa.ru" =>  ["yandex.ru", "pTbY9#eO5L4"],
];

function prepareAccessData(string $email, array $rawArray){
	$host = $password = $login = "";
	list($host, $password, $login) = array_map("trim", $rawArray);
	if( empty($login) )
		$login = $email;

	return compact("host", "login", "password");
}

$config = array();
foreach($data as $email => $info){
	$smtp = [];
	$imap = [];
    if( isset($info["smtp"]) ){
    	$smtp = prepareAccessData($email, $info["smtp"]);
    	$imap = prepareAccessData($email, $info["imap"]);
    } else {
    	$smtp = $imap = prepareAccessData($email, $info);
    }

    $config[$email] = compact("smtp", "imap");
}

return $config;