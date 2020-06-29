
import { Action, DocFile } from "@Root/types";
import { DOCS_LOAD_ALL, DOCS_LOAD_BY_TYPE } from "@Root/actions/DocsAction";

export interface IDocs
{
    company_card: DocFile[],
    our_prepayment_invoice: DocFile[],
    partners_prepayment_invoice: DocFile[],
    proxy_shipment_tk: DocFile[],
    contract: DocFile[],
    purchase_order: DocFile[],
    proxy_receipt_goods: DocFile[],
}

const initialState: IDocs = {
    company_card: [],
    our_prepayment_invoice: [],
    partners_prepayment_invoice: [],
    proxy_shipment_tk: [],
    contract: [],
    purchase_order: [],
    proxy_receipt_goods: [],
};

export default (state = initialState, action: Action) => {
    switch(action.type){
        case DOCS_LOAD_ALL:
            return action.payload

        case DOCS_LOAD_BY_TYPE:
            let newState = {...state}
            Object.assign(newState, action.payload)
            return newState
    }
    return state;
}
