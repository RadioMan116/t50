<?php
namespace Agregator\Common;

class NumberText
{
	const IDX_GENUS_MALE = 0;
	const IDX_GENUS_FEMALE = 1;
	const IDX_GENUS_AVERAGE = 2;

	private $number = -1;

	private $beforeTen = array("", array("один", "одна", "одно"), array("два", "две", "два"), "три", "четыре", "пять", "шесть", "семь", "восемь", "девять");

	private $beforeTwenty = array("десять", "одиннадцать", "двенадцать", "тринадцать", "четырнадцать" , "пятнадцать", "шестнадцать", "семнадцать", "восемнадцать", "девятнадцать");

	private $tens = array("", "десять", "двадцать", "тридцать", "сорок", "пятьдесят", "шестьдесят", "семьдесят" , "восемьдесят", "девяносто");

	private $hundreds = array("", "сто", "двести", "триста", "четыреста", "пятьсот", "шестьсот", "семьсот", "восемьсот", "девятьсот");

	private $unitsText = array(
		array("рубль"   ,"рубля"   ,"рублей", self::IDX_GENUS_MALE),
		array("тысяча"  ,"тысячи"  ,"тысяч", self::IDX_GENUS_FEMALE),
		array("миллион" ,"миллиона","миллионов", self::IDX_GENUS_MALE),
		array("миллиард","миллиарда","миллиардов", self::IDX_GENUS_MALE),
		array("триллион","триллиона","триллионов", self::IDX_GENUS_MALE),
		//array("дохрениллиард","дохрениллиарда","дохрениллиардов", self::IDX_GENUS_MALE),
	);

	public function setNum($num){
		$this->number = abs( (float) $num );
		return $this;
	}

	public function setGenus($genus){
		if( in_array($genus, array(0, 1, 2)) ){
			$this->unitsText[0][3] = $genus;
			$this->setUnitsText(null);
		}
		return $this;
	}

	public function setUnitsText($arrValues){
		if( $arrValues == null ){
			for($i = 0; $i < 3; $i++)
				$this->unitsText[0][$i] = "";
		} else {
			foreach($arrValues as $i => $val)
				$this->unitsText[0][$i] = $val;
		}
		return $this;
	}

	public function getText(){
		$number = $this->number;
		$this->number = -1;

		if( $number < 0 )
			return "";

		if( $number == 0 ){
			$endWord = $this->unitsText[0][$this->getEndingIndex(0)];
			return trim("ноль " . $endWord);
		}

		$numString = sprintf("%015.0f", $number);
		$words = array_reverse(str_split($numString, 3));
		foreach($words as $rankIndex => &$word){
			$genus = $this->unitsText[$rankIndex][3];
			$tmp = array();
			list($hundred, $ten, $unit) = array_map("intval", str_split($word, 1));
			$tmp[] = $this->hundreds[$hundred];
			$twoDigit = $ten * 10 + $unit;

			if( $twoDigit < 10 )
				$tmp[] = $this->getBeforeTen($twoDigit, $genus);
			elseif( $twoDigit < 20 )
				$tmp[] = $this->beforeTwenty[$unit];
			else
				$tmp[] = $this->tens[$ten] . " " . $this->getBeforeTen($unit, $genus);

			$word = trim(implode(" ", $tmp));

			if( !empty($word) || $rankIndex == 0 ){
				$endWord = $this->unitsText[$rankIndex][$this->getEndingIndex($twoDigit)];
				if( !empty($endWord) )
					$word .= " " . $endWord;
			}
		}
		return trim(implode(" ", array_reverse($words)));
	}

	private function getBeforeTen($num, $genus = 0){
		$word = $this->beforeTen[$num];
		if( is_array($word) )
			$word = $word[$genus];
		return $word;
	}

	function getEndingIndex($num){
		$num = abs($num);
		if( $num >= 10 && $num <= 19 )
			$num = 0;
		if( $num > 19 )
			$num = $num % 10;

		if( $num == 1 )
			return 0;

		if( $num > 1 and $num < 5)
			return 1;

		return 2;
	}
}
?>