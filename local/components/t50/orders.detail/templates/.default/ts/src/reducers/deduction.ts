
import { Action } from "@Root/types";
import { SelectItem } from "@Root/components/Select";
import { DEDUCTION_LOAD_TYPES, DEDUCTION_LOAD } from "@Root/actions/DeductionAction";


export type DeductionItem = {
    id: number,
    date: string,
    manager: number,
    deduction: number,
    comment: string,
    type: number,
}
export interface IDeduction
{
    items: DeductionItem[],
    types: SelectItem[],
}

const initialState: IDeduction = {
    items: [],
    types: []
};

export default (state = initialState, action: Action) => {
    switch(action.type){
        case DEDUCTION_LOAD:
            return {...state, items: action.payload}
        case DEDUCTION_LOAD_TYPES:
            return {...state, types: action.payload}
    }
    return state;
}
