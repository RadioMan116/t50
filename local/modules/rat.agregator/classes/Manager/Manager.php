<?php
namespace Agregator\Manager;

use Bitrix\Main\Application;
use \Bitrix\Main\UserTable;
use \Bitrix\Main\GroupTable;
use \Bitrix\Main\UserGroupTable;
use ORM\ORMInfo;
use Bitrix\Main\Entity;
use T50ArrayHelper;
use T50GlobVars;
use T50GlobCache;
use T50Text;

class Manager
{
    private static $userData;

    static function getList($groupsCode = null){
        $groupsIdCode = T50GlobVars::get("MANAGERS_GROUPS");

    	$filter = ['ACTIVE' => 'Y'];
    	if( isset($groupsCode) ){
    		if( !is_array($groupsCode) )
    			$groupsCode = [$groupsCode];

    		$groupsId = T50ArrayHelper::filterByKeys(array_flip($groupsIdCode), $groupsCode);
    		if( !empty($groupsId) )
    			$filter['GROUPS.GROUP_ID'] = array_values($groupsId);
    	}

    	$res = UserTable::getList(array(
    		'select' => ['ID', 'LOGIN', 'NAME', 'GROUPS.GROUP_ID', 'UF_GROUP'],
            'filter' => $filter, 'order' => ["NAME" => 'asc'],
    		'runtime' => [
				new Entity\ExpressionField('NAME', "TRIM(CONCAT(NAME,' ',LAST_NAME))"),
    			new Entity\ReferenceField('GROUPS', UserGroupTable::class, ['=this.ID' => 'ref.USER_ID']),
    		]
    	));

        $unitGroups = T50ArrayHelper::pluck(T50GlobVars::get("HLPROPS")["USER"]["UF_GROUP"], "id");
        $unitGroupsIdCode = array_flip($unitGroups);

    	$arResult = array();
    	while($result = $res->Fetch()){
    		$id = $result["ID"];
    		$groupId = $result["MAIN_USER_GROUPS_GROUP_ID"];
            $result["GROUP"] = $unitGroupsIdCode[$result["UF_GROUP"]];
    		unset($result["MAIN_USER_GROUPS_GROUP_ID"]);
            unset($result["UF_GROUP"]);

            if( !isset($arResult[$id]) ){
    			$result["GROUPS"] = [];
    			$arResult[$id] = $result;
    		}

    		if( isset($groupsIdCode[$groupId]) )
    			$arResult[$id]["GROUPS"][] = $groupsIdCode[$groupId];
    	}

    	return $arResult;
    }

    static function __callStatic($method, $args) {
        if( substr($method, 0, 3) == "can" )
            return self::checkAccess($method);
    }

    static function getCookie($name, $default = ""){
        $result = Application::getInstance()->getContext()->getRequest()->getCookie($name);
        return ( isset($result) ? $result : $default );
    }

    private static function checkAccess($_method){
        $method = substr($_method, 3);
        $method = strtoupper(T50Text::snakeCase($method));
        $flags = T50GlobVars::get("HLPROPS")["USER"]["UF_ACCESS_FLAGS"];
        if( !isset($flags[$method]) )
            throw new \RuntimeException("uncknow check access method \"{$_method}\"");

        if( self::isEmpowered() )
            return true;

        $data = self::getUser();
        $flagsId = $data["UF_ACCESS_FLAGS"] ?? [];
        return in_array($flags[$method]["id"], $flagsId);
    }

    static function getUser(){
        if( !isset(self::$userData) ){
            global $USER;
            $userId = $USER->getId();
            self::$userData = T50GlobCache::getRds(function () use($userId){
                $res = \CUser::GetByID($userId);
                $result = $res->Fetch();
                $result["GROUPS"] = \CUser::GetUserGroup($userId);
                return $result;
            }, "USER_{$userId}", 3600);
        }
        return self::$userData;
    }

    static function hasAccessToShop(int $shopId){
        if( self::isEmpowered() )
            return true;

        $data = self::getUser();
        return in_array($shopId, $data["UF_ACCESS_SHOPS"]);
    }

    static function isEmpowered(){
        if( TRUSTED_REMOTE_ACCESS === true )
            return true;
        $data = self::getUser();
        $groupsCodeId = T50GlobVars::get("MANAGERS_GROUPS", true);
        return in_array($groupsCodeId["extended_access"], $data["GROUPS"]);
    }

    static function getAvailableShops(){
        $shops = T50GlobVars::get("CACHE_SHOPS");
        $shops = array_column($shops, "NAME", "ID");
        if( self::isEmpowered() )
            return $shops;

        $data = self::getUser();
        $shops = T50ArrayHelper::filterByKeys($shops, (array) $data["UF_ACCESS_SHOPS"]);
        return $shops;
    }
}
