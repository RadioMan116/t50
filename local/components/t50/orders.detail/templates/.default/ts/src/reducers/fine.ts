
import { Action, Month } from "@Root/types";
import { FINE_LOAD_ALL} from "@Root/actions/FineAction";

export type FineItem = {
    id: number,
    date: string,
    manager_responsible: number,
    manager_initiator: number,
    fine: number,
    reason: string,
    month: Month,
}
export interface IFine
{
    items: FineItem[],
}

const initialState: IFine = {
    items: []
};

export default (state = initialState, action: Action) => {
    switch(action.type){
        case FINE_LOAD_ALL:
            return {items: action.payload}

    }
    return state;
}
