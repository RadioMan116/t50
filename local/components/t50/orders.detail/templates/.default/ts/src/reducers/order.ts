import { Action, Manager, OrderStatus } from "@Root/types";
import CommonAction, {  ORDER_LOAD_STATISTIC, ORDER_LOAD_DINAMIC, ORDER_CHANGE_SHARE_MANAGER } from "@Root/actions/CommonAction";
import { SelectItem } from "@Root/components/Select";
import { DeliveryCond } from "./delivery";
import Selectors from "@Root/tools/Selectors";


export interface IOrderData
{
    is_test: boolean,
    shop: string,
    order_id: number,
    remote_order_id: number,
    manager: number,
    source: number,
    status: number,
    city: number,
    dateCreate: string,
    agreed_client: boolean,
    agreed_supplier: boolean,
    currentManager: number,
    share_com_manager: number,
    date_invoice: string,
    phone_code_msk?: string,
    phone_code_spb?: string,
    managers: Manager[],
    statuses: SelectItem[],
    sources: SelectItem[],
    deliveryConditions: DeliveryCond[],
    installationProviders: SelectItem[],
    suppliers: SelectItem[],
    cities: SelectItem[],
}

const initialState: IOrderData = {
    is_test: false,
    shop: null,
    order_id: CommonAction.getOrderId(),
    remote_order_id: null,
    manager: null,
    source: null,
    status: null,
    city: null,
    dateCreate: null,
    agreed_client: false,
    agreed_supplier: false,
    currentManager: null,
    share_com_manager: null,
    date_invoice: null,
    managers: [],
    statuses: [],
    sources: [],
    deliveryConditions: [],
    installationProviders: [],
    suppliers: [],
    cities: [],
};

let staticFields = ["managers", "statuses", "sources", "deliveryConditions", "installationProviders", "suppliers", "cities"]

export default (state = initialState, action: Action) => {
    switch(action.type){
        case ORDER_LOAD_STATISTIC:
            let newState = {...state}
            staticFields.forEach(field => newState[field] = action.payload[field])
            return newState;

        case ORDER_LOAD_DINAMIC:
            return Selectors.updateStateExcept(action.payload, state, [...staticFields, "order_id"]);

        case ORDER_CHANGE_SHARE_MANAGER:
            return {...state, share_com_manager: action.payload}
    }
    return state;
}
