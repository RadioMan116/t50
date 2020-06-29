
import { Action,  /* InstallationItem,  */ } from "@Root/types";
import { PROFIT_LOAD_ALL } from "@Root/actions/ProfitAction";


export type ProfitBasketItem = {
    id: number,
    title: string,
    url: string,
    supplier_name: string,
    account: string,
    date: string,
    sale: number,
    purchase: number,
    logistics: number,
    commission: number,
    suppl_commission: number,
    diff: number,
    mounth_vp: number,
    mounth_zp: number,
    is_claim: boolean,
}

export type ProfitInstalItem =  {
    id: number,
    title: string,
    url: string,
    provider: string,
    date: string,
    account: string,
    sale: number,
    purchase: number,
    master: number,
    logistics: number,
    commission: number,
    suppl_commission: number,
    diff: number,
    mounth_vp: number,
    mounth_zp: number,
    is_claim: boolean,
}

export type ProfitBasketSum = {
    sale: number,
    purchase: number,
    logistics: number,
    commission: number,
    suppl_commission: number,
    diff: number,
}

export type ProfitInstalSum = {
    sale: number,
    purchase: number,
    master: number,
    logistics: number,
    commission: number,
    suppl_commission: number,
    diff: number,
}

export interface IProfit
{
    basket_items: ProfitBasketItem[],
    basket_sum: ProfitBasketSum,
    instal_items: ProfitInstalItem[],
    instal_sum: ProfitInstalSum,
    full_commission: number,
    delivery_date: string,
}

const initialState: IProfit = {
    basket_sum: {
        sale: null,
        purchase: null,
        logistics: null,
        commission: null,
        suppl_commission: null,
        diff: null,
    },
    instal_sum: {
        sale: null,
        purchase: null,
        master: null,
        logistics: null,
        commission: null,
        suppl_commission: null,
        diff: null,
    },
    basket_items: [],
    instal_items: [],
    full_commission: null,
    delivery_date: null,
};

export default (state = initialState, action: Action) => {
    if( action.type == PROFIT_LOAD_ALL )
        return action.payload;

    return state;
}
