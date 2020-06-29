import { Action } from "./types_consts";
import { INITIAL_LOAD, LOAD_SERVICES, LOAD_SERVICE, LOAD_ALL, SET_CUSTOM_INPUT, RESET_ALL } from "./actions";
import { SelectItem } from "./components/Select";


export interface IRootReducer {
    providers: SelectItem[],
    provider: string,
    categories: SelectItem[],
    category_id: number,
    services?: SelectItem[],
    service_id: number,
    installation_id: number,
    price?: number,
    custom_input?: string,
}

const initialState: IRootReducer = {
    providers: [],
    provider: null,
    categories: [],
    category_id: null,
    services: null,
    service_id: null,
    installation_id: null,
    price: null,
    custom_input: null,
};

const RootReducer = (state = initialState, action: Action) => {
    switch (action.type) {
        case INITIAL_LOAD:
            return {
                ...state,
                providers: action.payload.providers,
                provider: action.payload.provider,
                categories: action.payload.categories,
                services: null,
                category_id: null,
                service_id: null,
                price: null,
            }

        case LOAD_SERVICES:
            return {
                ...state,
                category_id: action.payload.category_id,
                services: action.payload.services,
                service_id: null,
                price: null,
            }

        case LOAD_SERVICE:
            return {
                ...state,
                service_id: action.payload.service_id,
                price: action.payload.price,
            }

        case LOAD_ALL:
            let newState = action.payload;
            newState.custom_input = state.custom_input
            return newState;

        case SET_CUSTOM_INPUT:
            return {...state, custom_input: action.payload}

        case RESET_ALL:
            return initialState
    }

    return state;
}

export default RootReducer