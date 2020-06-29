
import { Action } from "@Root/types";
import { DELIVERY_LOAD_ALL } from "@Root/actions/DeliveryAction";

export type DeliveryCond = {val: number, title: string, pickup?: boolean, tk?: boolean}
export type BasketDeliveryItem = {
    id: number,
    basket_id: number,
    condition: number,
    date: string,
    time: string,
    time_range: string[]
    pickup_address: string,
    cost: number,
    cost_us: boolean,
    mkad_price: number,
    mkad_km: number,
    mkad_sum: number,
    mkad_us: boolean,
    vip: number,
    vip_us: boolean,
    lift: number,
    lift_us: boolean,
}

export interface IDelivery
{
    items: BasketDeliveryItem[],
    price_client: number,
    price_shop: number,
}

const initialState: IDelivery = {
    items: [],
    price_client: 0,
    price_shop: 0,
};

export default (state = initialState, action: Action) => {
    switch(action.type){
        case DELIVERY_LOAD_ALL:
            return action.payload
    }
    return state;
}