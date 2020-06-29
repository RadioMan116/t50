<?php
namespace Agregator\Manager\JSON;

use T50ArrayHelper;

class NewsInfo
{
	private $oldJson;

	const UNREAD_LIMIT = 10;
	const FAVORITE_LIMIT = 20;

	private $unread = [];
	private $favorite = [];

	function __construct($json = null){
		if( isset($json) )
			$this->setJson($json);
	}

	function setJson($json){
		$this->oldJson = $json;
		$data = json_decode($json, true) ?? [];
		$this->unread = $this->prepare($data["unread"], self::UNREAD_LIMIT);
		$this->favorite = $this->prepare($data["favorite"], self::FAVORITE_LIMIT);
		return $this;
	}

	function addUnread(int $newsId){
		if( $newsId > 0 ){
			$this->unread[] = $newsId;
			$this->unread = $this->prepare($this->unread, self::UNREAD_LIMIT);
		}

		return $this;
	}

	function setAsRead(int $newsId){
		if( $newsId > 0 )
			$this->unread = T50ArrayHelper::remItem($this->unread, $newsId);

		return $this;
	}

	function getUnread(){
		return array_values($this->unread);
	}


	function addFavorite(int $newsId){
		if( $newsId > 0 ){
			$this->favorite[] = $newsId;
			$this->favorite = $this->prepare($this->favorite, self::FAVORITE_LIMIT);
		}

		return $this;
	}

	function removeFavorite(int $newsId){
		if( $newsId > 0 )
			$this->favorite = T50ArrayHelper::remItem($this->favorite, $newsId);

		return $this;
	}

	function getFavorite(){
		return array_values($this->favorite);
	}

	function getJson(){
		return json_encode(["unread" => $this->unread, "favorite" => $this->favorite]);
	}

	function isChanged(){
		return $this->oldJson != $this->getJson();
	}

	private function prepare($list, $limit = 0){
		if( !is_array($list) )
			return [];

		$list = array_unique($list);

		$list = T50ArrayHelper::filterMap($list, function (&$id){
            $id = (int) $id;
            return ( $id > 0 );
        });

        if( $limit > 0 )
        	$list = array_slice($list, -$limit);

        return $list;
	}
}