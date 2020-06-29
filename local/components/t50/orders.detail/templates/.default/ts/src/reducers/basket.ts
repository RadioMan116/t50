
import { Action, PriceComments } from "@Root/types";
import { BASKET_LOAD_ALL} from "@Root/actions/BasketAction";

export type PayType = {val: number, title: string}
export type BasketItem = {
    id: number,
    supplier_id: number,
    product_id: number,
    title: string,
    url: string,
    quantity: number,
    sale: number,
    purchase: number,
    is_manual_sale: boolean,
    is_manual_purchase: boolean,
    commission: number,
    pay_type: number,
    claim: boolean,
    claim_replace: boolean,
    comments: PriceComments
}

export type BasketSum = {
    sale_sum: number,
    purchase_sum: number,
    commission_sum: number,
}
export type IBasket =
{
    items: BasketItem[],
    pay_types: PayType[]
} & BasketSum

const initialState: IBasket = {
    items: [],
    sale_sum: 0,
    purchase_sum: 0,
    commission_sum: 0,
    pay_types: []
};

export default (state = initialState, action: Action) => {
    switch(action.type){
        case BASKET_LOAD_ALL:
            return action.payload
    }
    return state;
}
