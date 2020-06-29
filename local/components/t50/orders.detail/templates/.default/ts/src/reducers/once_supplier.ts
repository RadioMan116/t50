
import { Action } from "@Root/types";
import { ONCE_SUPPLIER_DELIVERY, ONCE_SUPPLIER_ACCOUNT, ONCE_SUPPLIER_INSTALL } from "@Root/actions/OnceSupplierAction";

export interface IOnceSupplier
{
    delivery: boolean,
    accounts: boolean,
    install: boolean,
}

const initialState: IOnceSupplier = {
    delivery: false,
    accounts: false,
    install: false,
};

export default (state = initialState, action: Action) => {
    switch(action.type){
        case ONCE_SUPPLIER_DELIVERY:
            return {...state, delivery: action.payload}

        case ONCE_SUPPLIER_ACCOUNT:
            return {...state, accounts: action.payload}

        case ONCE_SUPPLIER_INSTALL:
            return {...state, install: action.payload}
    }
    return state;
}