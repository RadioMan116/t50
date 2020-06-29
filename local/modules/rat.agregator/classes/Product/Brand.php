<?php
namespace Agregator\Product;

use T50DB;
use rat\agregator\Formula;
use Agregator\IB\Element;
use Agregator\IB\Elements;

class Brand
{
	static function setFormula(int $brandId, int $formulaId){
		if( $brandId <= 0 || $formulaId < 0 )
			return false;

		$brand = (new Elements("brands"))->props("FORMULA")->getOneFetchById($brandId);

		if( $formulaId > 0 ){
			$formula = Formula::clas()::getRowById($formulaId);
			if( empty($formula) )
				return false;
		}

		if( empty($brand) )
			return false;

		if( $brand["PROPERTY_FORMULA_VALUE"] ==  $formulaId )
			return true;

		T50DB::startTransaction();

		if( !(new Element("brands"))->update($brandId, [], ["FORMULA" => $formulaId]) )
			return T50DB::rollback();

		if( !Formula::clas()::update($formulaId, ["UF_UNDELETABLE" => 1])->isSuccess() )
			return T50DB::rollback();

		return T50DB::commit();
	}

	static function deleteFormula(int $formulaId){
		if( $formulaId <= 0 )
			return false;

		$res = (new Elements("brands"))->filter(["FORMULA" => $formulaId])->get();
		$brand = (new Element("brands"));
		while( $result = $res->Fetch() )
			$brand->update($result["ID"], [], ["FORMULA" => false]);
		return true;
	}

	static function getMapBrandFormula(){
		$res = (new Elements("brands"))->filter([">PROPERTY_FORMULA" => 0])->props("FORMULA")->get();
		$arResult = [];
		while( $result = $res->Fetch() )
			$arResult[$result["ID"]] = $result["PROPERTY_FORMULA_VALUE"];

		return $arResult;
	}
}