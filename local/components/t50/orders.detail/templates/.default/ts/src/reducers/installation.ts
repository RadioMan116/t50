
import { Action, PriceComments } from "@Root/types";
import {INSTALLATION_LOAD_ALL } from "@Root/actions/InstallationAction";


export type InstallationItem = {
    id: number,
    basket_id: number,
    order_id: number,
    product_name: string,
    provider: string,
    service_id: number,
    date: string,
    sale: number,
    purchase: number,
    costs_us: boolean,
    visit_master_us: boolean,
    visit_master_km: number,
    visit_master_km_price: number,
    visit_master_price: number,
    visit_master_sum: number,
    commission: number,
    comments: PriceComments,
    comment: string,
    type: string,
    can_change: boolean,
}

export interface IInstallation
{
    items: InstallationItem[],
    price_client: number,
    price_shop: number,
    remcityOrderNum: number,
}

const initialState: IInstallation = {
    items: [],
    price_client:  null,
    price_shop: null,
    remcityOrderNum: null,
};

export default (state = initialState, action: Action) => {
    switch(action.type){
        case INSTALLATION_LOAD_ALL:
            return action.payload
    }
    return state;
}
