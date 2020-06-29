
import { Action } from "@Root/types";
import { ACCOUNT_LOAD_ITEMS, ACCOUNT_LOAD_ITEM } from "@Root/actions/AccountAction";


export type AccountsItem = {
    id: number,
    basket_id: number,
    account: string,
    account_product: string[],
    date_arrival: string[],
    account_delivery: string[],
    official_our: string[],
    official_partners: string[],
    account_tn_tk: string[],
    in_stock: boolean,
    shipment: boolean,
}

export interface IAccounts
{
    items: AccountsItem[],
}

const initialState: IAccounts = {
    items: [],
};

export default (state = initialState, action: Action) => {
    switch(action.type){
        case ACCOUNT_LOAD_ITEMS:
            return {...state, items: action.payload}

        case ACCOUNT_LOAD_ITEM:
            return changeItem(state, action.payload)
    }
    return state;
}

const changeItem = (state:IAccounts, newItem: AccountsItem) => {
    let items = state.items.map(item => {
        if( item.id == newItem.id ){
            return newItem
        }
        return item;
    })
    return {...state, items}
}
