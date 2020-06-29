<?php

namespace Agregator\Sync;

class Curl
{
	private $post;
	private $savePath;
	private $info;
	private $returnBool = false;
	private $returnBoolMessage;

	function setPost(array $post = array()){
		$this->post = $post;
		return $this;
	}

	function setFile($file){
		@unlink(file_exists($file));
		$this->savePath = $file;
		return $this;
	}

	function retBool(){
		$this->returnBool = true;
		$this->returnBoolMessage = null;
		return $this;
	}

	function get($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SAFE_UPLOAD, 1);

		if( !empty($this->post) ){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $this->post);
		}

		if( !empty($this->savePath) )
			return $this->saveToFile($ch);

		return $this->getData($ch);
	}

	function getInfo(){
		return $this->info;
	}

	function getLastMessage(){
		return $this->returnBoolMessage;
	}

	private function close($ch){
		$this->info = array(
			"HTTP_CODE" => curl_getinfo($ch, CURLINFO_HTTP_CODE),
			"ERROR_NUM" => curl_errno($ch),
			"ERROR_MESSAGE" => curl_error($ch),
		);
		$this->post = $this->returnBool = $this->savePath = null;
		curl_close($ch);
	}

	private function getData($ch){
		$data = curl_exec($ch);
		$returnBool = $this->returnBool;
		$this->close($ch);

		if( $returnBool ){
			$this->returnBoolMessage = $data;
			return ( trim($data) == "ok" );
		}
		return $data;
	}

	private function saveToFile($ch){
		@unlink($this->savePath);
		if( file_exists($this->savePath) ){
			$this->close($ch);
			return false;
		}
		$fp = fopen($this->savePath, 'wb');
		curl_setopt($ch, CURLOPT_FILE, $fp);
		$result = curl_exec($ch);
		$result = ( $result === true ? $this->savePath : false );
		fclose($fp);
		$this->close($ch);
		if( $this->info["HTTP_CODE"] != 200 )
			return false;
		return $result;
	}
}