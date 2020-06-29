<?php

namespace Agregator\Integration\RemCity;

class Api
{
	use Traits\Logger;

	private $accessData;

	function __construct(){
        $this->accessData = (new Manager)->getAccess();
	}

    function __call($method, $args){
        $methodsMap = array(
            "getDict" => "/dict/",
            "getTypes" => "/model-types/",
            "getServices" => "/services-price/",
            "getOrders" => "/orders/",
        );
        if( isset($methodsMap[$method]) )
            return $this->send($methodsMap[$method]);
    }

    function sendPost($query, array $data = array()){
        return $this->send($query, $data, $method = "POST");
    }

    private function send($query, array $postData = array(), $method = "GET"){
        global $APPLICATION;

        $postDataJson = $APPLICATION->ConvertCharsetArray($postData, "windows-1251", "utf-8");
        $postDataJson = json_encode($postDataJson);

        $url = "https://remcity.net/api/v1" . $query;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERPWD,  $this->accessData->login . ":" . $this->accessData->password);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'x-api-key: ' . $this->accessData->apiKey,
            "Content-Type: application/json",
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if( $method == "POST" ){
            curl_setopt($ch, CURLOPT_POST, true);
            if( !empty($postData) ){
               curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataJson);
            }
        }
        $response = curl_exec($ch);
        $response = trim(preg_replace("#(<.*>)#s", "", $response));
        curl_close($ch);
        $arResponse = json_decode($response, true);
        if( $arResponse["status"] != "success" ){
            $this->log(array(
                "query: {$query}",
                "width postData:", var_export($postData, true),
                "width json postData:", var_export($postDataJson, true),
                "return:",  var_export($response, true)
            ));
            return false;
        }

        $data = $arResponse["data"];

        return $data;
    }
}