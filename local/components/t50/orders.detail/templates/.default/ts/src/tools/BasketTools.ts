import { store } from "@Root/Stor"
import { BasketItem } from "@Root/reducers/basket"
import { SelectItem } from "@Root/components/Select"

export default class BasketTools
{
    static getById(basket_id: number){
        return store.getState().basket.items.find(item => item.id == basket_id)
    }

    static getByIdField(basket_id: number, field: keyof BasketItem){
        let basket = BasketTools.getById(basket_id)
        if( basket == null )
            return
        return basket[field]
    }

    static hasClaim(){
        let withClaim = store.getState().basket.items.find(item => item.claim)
        return  withClaim != null
    }

    static getBasketSuppliers(basketItems: BasketItem[]): SelectItem[]{
        let suppliersName = store.getState().order.suppliers
        if( basketItems == null || basketItems.length == 0 || suppliersName == null || suppliersName.length == 0 )
            return [];

        let supplierId = basketItems.map(item => item.supplier_id);
        supplierId = supplierId.filter((id, i) => supplierId.indexOf(id) === i);

        return suppliersName.filter(item => supplierId.indexOf(item.val as number) != -1 );
    }
}