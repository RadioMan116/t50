import { CHANGE_CHECK_RRC, CHANGE_PARAMETERS } from "@Root/consts";
import { store } from "@Root/Stor";
import { FORMULA_PARAMS_BLOCK } from "@Root/types";

export const changeCheckRRC = (val: boolean) => {
    store.dispatch({
        type: CHANGE_CHECK_RRC,
        payload: val
    })
};

export const changeParameters = (val: FORMULA_PARAMS_BLOCK[]) => ({
    type: CHANGE_PARAMETERS,
    payload: val
});