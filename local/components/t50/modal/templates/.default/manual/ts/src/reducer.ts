import { Action, ModalKey } from "./types_consts";
import { LOADL_ALL, CHANGE_DATE, CHANGE_COMMENT, CHANGE_VALUE } from "./actions";

export interface IRootReducer
{
    modal_key?: ModalKey,
    date_end?: string,
    comment: string,
    value: number,
}

const initialState: IRootReducer = {
    modal_key: null,
    date_end: "",
    comment: "",
    value: 0,
};

const RootReducer =  (state = initialState, action: Action) => {
    switch(action.type){
        case LOADL_ALL:
            let newState = {... state}
            for(let code in action.payload)
                newState[code] = action.payload[code]

            return newState
        case CHANGE_DATE:
            return {...state, date_end: action.payload}
        case CHANGE_COMMENT:
            return {...state, comment: action.payload}
        case CHANGE_VALUE:
            return {...state, value: action.payload}
    }
    return state;
}

export default RootReducer