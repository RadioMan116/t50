<?php

namespace Agregator\Integration\RemCity;

use CUser;

class Manager
{
    use Traits\Logger;

    const APIKEY_RAT = "1c1d218ebb6c3c81a29db6ad7fffe41a";
    const APIKEY_HD = "5b51ab3ca0ec609e6bc7332339b9f39f";

    const DEFAULT_LOGIN = "manager1074";
    const DEFAULT_PASSWORD = "K34026";

    const GR_RAT = "GR_RAT";
    const GR_HD = "GR_HD";

    private $data;

    function __construct(){
        $this->initData();
    }

    function getAccess(){
        $data = $this->parseLoginPassword();
        $apiKey = $this->getApiKey();
        $data->apiKey = $apiKey;
        return $data;
    }

    private function parseLoginPassword(){
        $data = new \StdClass();
        $data->login = self::DEFAULT_LOGIN;
        $data->password = self::DEFAULT_PASSWORD;

        if( !isset($this->data) || empty($this->data["UF_REMCITY_ACCESS"]))
            return $data;

        $parts = preg_split("#\s+#", trim($this->data["UF_REMCITY_ACCESS"]));
        if( count($parts) != 2 )
            return $data;

        list($login, $password) = $parts;

        if( empty($login) || empty($password) ){
            $this->log("cannot detect login and password");
            return $data;
        }

        $data->login = $login;
        $data->password = $password;

        return $data;
    }

    private function getApiKey(){
        switch ($this->detectGroup()) {
            case self::GR_HD:
                return self::APIKEY_HD;
            case self::GR_RAT:
                return self::APIKEY_RAT;
        }

        return self::APIKEY_RAT;
    }

    private function detectGroup(){
        return self::GR_RAT;
    }

    private function initData(){
        global $USER;
        $res = CUser::GetByID($USER->GetID());
        $data = $res->Fetch();
        if( !empty($data) )
            $this->data = $data;
    }
}
