<?php

$tabNames = array(
	"basket" => "Состав заказа",
	"client" => "Данные клиента",
	"delivery" => "Условия доставки",
	"installation" => "Установка",
	"accounts" => "Номера счетов и заказов",
	"docs" => "Документы",
	"comments" => "Комментарии",
	"fine" => "Штрафы",
	"deduction" => "Вычеты",
	"profit" => "Прибыль по заказу",
	"reclamation" => "Рекламации",
	"history" => "Логи",
);

$viewDefault = array("TYPE" => "ALL",  "BLOCKS" => []);
foreach($tabNames as $code => $title){
    $viewDefault["BLOCKS"][] = array(
    	"code" => $code,
    	"title" => $title,
    	"notab" => ($code == "reclamation"),
    );
}

return compact("tabNames", "viewDefault");
