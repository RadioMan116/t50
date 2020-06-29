<?php
namespace Agregator\Manager\JSON;
use CUser;
use T50ArrayHelper;
use \Bitrix\Main\GroupTable;
use Bitrix\Main\UserGroupTable;
use T50DB;
use T50GlobVars;

class ManagerNews
{
    const UNREAD = "unread";
    const FAVORITE = "favorite";
    const PROP_CODE = "UF_NEWS_INFO";

    function setReadAndUnreadForManagers(int $newsId, array $readManagersId, array $unreadManagersId){
        if( !T50ArrayHelper::isInt($readManagersId) || !T50ArrayHelper::isInt($unreadManagersId) )
            return false;

        $readManagersId = array_diff($readManagersId, $unreadManagersId);
        $managersId = array_unique(array_merge($readManagersId, $unreadManagersId));

        $res = $this->getUserRes($managersId);
        $user = new CUser;
        T50DB::startTransaction();
        while( $result = $res->Fetch() ){
            $managerId = $result["ID"];
            $newsInfo = new NewsInfo($result[self::PROP_CODE]);
            if( in_array($managerId, $readManagersId) ){
                $newsInfo->setAsRead($newsId);
            } else {
                $newsInfo->addUnread($newsId);
            }
            if( !$this->saveNewsInfoForUser($result["ID"], $newsInfo) )
                return T50DB::rollback();
        }
        return T50DB::commit();
    }

    function addUnreadForManagerGroups(int $newsId, array $groupsId){
        $managersForAddUnread = $this->getManagersByGroups($groupsId);
        $managersForRemoveUnread = $this->getManagersByGroups($groupsId, true);
        return $this->setReadAndUnreadForManagers($newsId, $managersForRemoveUnread, $managersForAddUnread);
    }

    function getUnread(){
        $newsInfo = $this->getNewsInfoForUser($GLOBALS["USER"]->GetId());
        return $newsInfo->getUnread();
    }

    function setAsReaded(int $newsId){
        $userId = $GLOBALS["USER"]->GetId();
        $newsInfo = $this->getNewsInfoForUser($userId);
        $newsInfo->setAsRead($newsId);
        return $this->saveNewsInfoForUser($userId, $newsInfo);
    }

    function getFavorite(){
        $newsInfo = $this->getNewsInfoForUser($GLOBALS["USER"]->GetId());
        return $newsInfo->getFavorite();
    }

    function addFavorite(int $newsId){
        $userId = $GLOBALS["USER"]->GetId();
        $newsInfo = $this->getNewsInfoForUser($userId);
        $newsInfo->addFavorite($newsId);
        return $this->saveNewsInfoForUser($userId, $newsInfo);
    }

    function removeFavorite(int $newsId){
        $userId = $GLOBALS["USER"]->GetId();
        $newsInfo = $this->getNewsInfoForUser($userId);
        $newsInfo->removeFavorite($newsId);
        return $this->saveNewsInfoForUser($userId, $newsInfo);
    }

    function getGroups(){
        static $groups;
        if( isset($groups) )
            return $groups;

        $groups = array();
        $res = GroupTable::getList([
            "filter" => [">=C_SORT" => 100],
            "select" => ["NAME", "ID"],
            "order" => ["NAME" => "ASC"],
        ]);
        while( $result = $res->Fetch() )
            $groups[] = $result;

        return $groups;
    }

    private function getNewsInfoForUser(int $userId): NewsInfo{
        $data = $this->getUserRes($userId)->Fetch();
        $newsInfo = new NewsInfo($data[self::PROP_CODE]);
        return $newsInfo;
    }

    private function getManagersByGroups(array $groupsId, $except = false){
        $groupsId = $this->getValidGroups($groupsId, $except);
        if( empty($groupsId) )
            return [];

        $data = UserGroupTable::getList(["filter" => ["GROUP_ID" => $groupsId]])->fetchAll();
        $managersId = array_unique(array_column($data, "USER_ID"));
        return $managersId;
    }

    private function getValidGroups(array $groupsId, $except = false){
        if( !T50ArrayHelper::isInt($groupsId) )
            return [];

        $validGroupsId = array_column($this->getGroups(), "ID");
        if( $except ){
            $groups = array_diff($validGroupsId, $groupsId);
        } else {
            $groups = array_intersect($groupsId, $validGroupsId);
        }

        return array_values($groups);
    }

    private function getUserRes($userId){
        $getOnce = true;
        if( is_array($userId) ){
            $getOnce = false;
            $userId = implode("|", $userId);
        }
        $params = ["FIELDS" => ["ID"], "SELECT" => [self::PROP_CODE]];
        $res = CUser::getList($by = "ID", $order = "asc" , ["ID" => $userId], $params);
        return $res;
    }

    private function saveNewsInfoForUser(int $userId, NewsInfo $newsInfo){
        static $user;
        $user = $user ?? new CUser;
        if( !$newsInfo->isChanged() )
            return true;
        return $user->Update($userId, [self::PROP_CODE => $newsInfo->getJson()]);
    }
}