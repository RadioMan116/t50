import { Action, FORMULA_PARAMS_BLOCK } from "@Root/types";
import { CHANGE_CHECK_RRC, CHANGE_PARAMETERS } from "@Root/consts";

export interface IParameters
{
    blocks: FORMULA_PARAMS_BLOCK[],
    check_rrc: boolean
}

const initialState: IParameters = {
    blocks: [
        // {percent: 7, min_purchase: 5347, max_purchase: 155500, max_commission: 6000, min_commission: 2000},
        // {percent: 7, min_purchase: 5347, max_purchase: 155500, max_commission: 6000, min_commission: 2000},
    ],
    check_rrc: true
};

export default (state = initialState, action: Action) => {
    switch(action.type){
        case CHANGE_CHECK_RRC:
            return {...state, check_rrc: action.payload}

        case CHANGE_PARAMETERS:
            return {...state, blocks: action.payload}
    }
    return state;
}
