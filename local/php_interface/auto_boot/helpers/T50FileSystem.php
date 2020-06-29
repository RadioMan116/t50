<?php
class T50FileSystem
{
	public static function initDir($dir, $message = ""){
		$root = $_SERVER["DOCUMENT_ROOT"];
		if( empty($root) )
			throw new \RuntimeException("empty DOCUMENT_ROOT for initDir {$dir}");

		// if absolute path
		$relativeDir = str_replace($root, "", $dir);
		$relativeDir = trim($relativeDir, "/");
		$dir = $root . "/" . $relativeDir;

		if( is_dir($dir) )
			return $dir;

		$oldmask = umask(0);
		$created = mkdir($dir, 0777, true);
		umask($oldmask);

		if( !$created ){
			if( empty($message) )
				$message = "T50FileSystem: cannot create dir {$dir}";
			throw new \RuntimeException($message);
		}

		return $dir;
	}

	public static function createTmpFile(&$tmpFile, $data = null){
		$tmpFile = tmpfile();
		if( isset($data) )
			fwrite($tmpFile, $data);
		$metaDatas = stream_get_meta_data($tmpFile);
		$path = $metaDatas['uri'];
		return $path;
	}
}
?>