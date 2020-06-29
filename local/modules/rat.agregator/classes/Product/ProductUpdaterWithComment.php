<?php
namespace Agregator\Product;

use rat\agregator\ProductComment;
use rat\agregator\Product as ProductOrm;
use T50DB;

class ProductUpdaterWithComment
{
	private static $syncClass = \Agregator\Sync\Sale\Sale::class; // for tests

	static function setDiscontinued(int $id, bool $discontinued, $comment){
		T50DB::startTransaction();

		if( $discontinued && !ProductComment::setDiscontinuedComment($id, $comment) )
			return T50DB::rollback();

		$res = ProductOrm::clas()::update($id, ["UF_DISCONTINUED" => ( $discontinued ? 1 : 0 )]);
		if( !$res->isSuccess() )
			return T50DB::rollback();

		$product = Product::getById($id);
		if( !$product->save() )
			return T50DB::rollback();

		T50DB::commit();

		self::$syncClass::syncOnce($product->id, $product->arShopsId);

		return true;
	}

	static function setAnalog(int $baseId, int $analogId, $comment){
		T50DB::startTransaction();

		if( !ProductComment::setAnalogComment($baseId, $comment) )
			return T50DB::rollback();

		if( $analogId <= 0 )
			return T50DB::rollback();

		$res = ProductOrm::clas()::update($baseId, ["UF_ANALOG_ID" => $analogId]);
		if( !$res->isSuccess() )
			return T50DB::rollback();

		return T50DB::commit();
	}

	static function setManualPurchaseForSupplier(int $id, int $supplierId, $purchase, $comment = "", $date = null){
		T50DB::startTransaction();
		if( !ProductComment::setCommentForSupplierPurchase($id, $supplierId, $comment, $date) )
			return T50DB::rollback();

		$product = Product::getById($id);
		$product->Market->setSupplier($supplierId);
		$autoMode = ( ProductComment::getLastOperation() == ProductComment::DELETE );
		$product->Market->setPurchaseMode($autoMode);
		if( !$autoMode )
			$product->Market->setPurchaseManual($purchase);

		if( !$product->save() )
			return T50DB::rollback();

		T50DB::commit();

		self::$syncClass::syncOnce($product->id, $product->arShopsId);

		return true;
	}

	static function setManualAvailForSupplier(int $id, int $supplierId, int $avail, $comment = "", $date = null){
		T50DB::startTransaction();
		if( !ProductComment::setCommentForSupplierAvail($id, $supplierId, $comment, $date) )
			return T50DB::rollback();

		$product = Product::getById($id);
		$product->Market->setSupplier($supplierId);
		$autoMode = ( ProductComment::getLastOperation() == ProductComment::DELETE );
		$product->Market->setAvailMode($autoMode);
		if( !$autoMode )
			$product->Market->setAvailManual($avail);

		if( !$product->save() )
			return T50DB::rollback();

		T50DB::commit();

		self::$syncClass::syncOnce($product->id, $product->arShopsId);

		return true;
	}
}