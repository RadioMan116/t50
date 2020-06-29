import { CHANGE_CALC_FROM_SALE_PRICE, CHANGE_PRICE_TYPE, CHANGE_LOADING_STATUS, CHANGE_CITY, ADD_LOG, CLEAR_LOG } from "@Root/consts";
import { store } from "@Root/Stor";
import { PRICE_TYPE, CITY } from "@Root/types";

export const changeCalcFromSalePrice = (val: boolean) => {
    store.dispatch({
        type: CHANGE_CALC_FROM_SALE_PRICE,
        payload: val
    })
};

export const changePriceType = (val: PRICE_TYPE) => {
    store.dispatch({
        type: CHANGE_PRICE_TYPE,
        payload: val
    })
};

export const changeLoadingStatus = (val: boolean) => ({
    type: CHANGE_LOADING_STATUS,
    payload: val
});

export const changeCity = (val: CITY) => {
    store.dispatch({
        type: CHANGE_CITY,
        payload: val
    })
};

export const addLog = (log: string) => {
    store.dispatch({
        type: ADD_LOG,
        payload: log
    })
};

export const clearLog = () => {
    store.dispatch({
        type: CLEAR_LOG,
        payload: null
    })
};