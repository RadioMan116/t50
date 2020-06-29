import { Action, PRICE_TYPE, CITY } from "@Root/types";
import { CHANGE_CALC_FROM_SALE_PRICE, CHANGE_PRICE_TYPE, CHANGE_LOADING_STATUS, CHANGE_CITY, ADD_LOG, CLEAR_LOG } from "@Root/consts";

export interface ICommonData
{
    priceType: PRICE_TYPE,
    calcFromSalePrice: boolean,
    loading_status: boolean,
    city: CITY,
    logs: string[],
}

const initialState: ICommonData = {priceType: "rrc", calcFromSalePrice: false, loading_status: false, city: "MSK", logs: []};

export default (state = initialState, action: Action) => {
    switch(action.type){
        case CHANGE_CALC_FROM_SALE_PRICE:
            return {...state, calcFromSalePrice: action.payload}

        case CHANGE_PRICE_TYPE:
            return {...state, priceType: action.payload}

        case CHANGE_LOADING_STATUS:
            return {...state, loading_status: action.payload}

        case CHANGE_CITY:
            return {...state, city: action.payload}

        case ADD_LOG:
            return {...state, logs: [...state.logs, action.payload]}

        case CLEAR_LOG:
            return {...state, logs: []}
    }
    return state;
}
