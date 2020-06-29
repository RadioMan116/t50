<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if( !function_exists("filterInput") ){
function filterInput($input, $sizeClassSfx){
	switch ($input["TYPE"]) {
		case "SELECT":
		case "MSELECT":
			include __DIR__ . "/include/inputs/select.php";
			break;
		case "RANGE":
			include __DIR__ . "/include/inputs/range.php";
			break;
	}
}
}
?>