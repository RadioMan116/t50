<?php
class T50Text
{
	public static function translit($text, $replacesSpace = false){
		static $translitAr = array(
			"а" => "a","б" => "b","в" => "v","г" => "g","д" => "d","е" => "e",
			"ж" => "j","з" => "z","и" => "i","й" => "y","к" => "k","л" => "l",
			"м" => "m","н" => "n","о" => "o","п" => "p","р" => "r","с" => "s",
			"т" => "t","у" => "u","ф" => "f","х" => "h","ц" => "c","ч" => "ch",
			"ш" => "sh","щ" => "sh","ъ" => "","ы" => "i","ь" => "","э" => "e",
			"ю" => "u","я" => "ya","А" => "A","Б" => "B","В" => "V","Г" => "G",
			"Д" => "D","Е" => "E","Ж" => "J","З" => "Z","И" => "I","Й" => "Y",
			"К" => "K","Л" => "L","М" => "M","Н" => "N","О" => "O","П" => "P",
			"Р" => "R","С" => "S","Т" => "T","У" => "U","Ф" => "F","Х" => "H",
			"Ц" => "C","Ч" => "CH","Ш" => "SH","Щ" => "SH","Ъ" => "","Ы" => "I",
			"Ь" => "","Э" => "E","Ю" => "U","Я" => "YA","ё" => "e","Ё" => "E",
		);

		$result = str_replace(array_keys($translitAr), array_values($translitAr), $text);
		if( is_string($replacesSpace) )
			$result = preg_replace("#\s#", $replacesSpace, $result);

		return $result;
	}

	public static function formatBytes($bytes = 0){
		if( $bytes == 0 )
			return "0 b";

		$units = array('b', 'kb', 'mb', 'gb');
		$pow = floor(log($bytes) / log(1024));
		$result = round($bytes / pow(1024, $pow));

		return $result . ' ' . $units[$pow];
	}

	public static function camelCase($text, $ucfirst = true){
		$text = trim($text);
		$parts = preg_split("#[\s_\.]+#", $text);
		for($i = 0; $i < count($parts); $i++){
			if( $i == 0 && !$ucfirst )
				continue;
			$parts[$i] = ucfirst($parts[$i]);
		}
		return implode("", $parts);
	}

	public static function snakeCase($text){
		$text = trim($text);
		if( preg_match_all("#((?:[A-Z\s\.]|^)[^A-Z\s\.]+)#", $text, $matches) == 0 )
			return $text;
		$matches = array_map(function ($val){
			return strtolower(trim($val));
		}, $matches[1]);
		return implode("_", $matches);
	}

	public static function random($len = 5){
		$sims = range("a", "z");
		shuffle($sims);
		$sims = array_slice($sims, 0, $len);
		return implode("", $sims);
	}

	public static function wordEnding(int $count, array $words, $withCount = true){
		if( count($words) != 3 )
			return "";

		$cnt = abs($count);
		if( $cnt >= 10 && $cnt <= 19 )
			$cnt = 0;
		if( $cnt > 19 )
			$cnt = $cnt % 10;

		if( $cnt == 1 ){
			$index = 0;
		} elseif( $cnt > 1 and $cnt < 5){
			$index = 1;
		} else {
			$index = 2;
		}

		return ( $withCount ? "{$count} " : "" ) . $words[$index];
	}
}
?>
