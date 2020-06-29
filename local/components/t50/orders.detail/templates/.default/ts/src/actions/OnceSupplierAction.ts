import Action from "./Action";
import { IClient } from "@Root/reducers/client";
import { store } from "@Root/Stor";

export const ONCE_SUPPLIER_DELIVERY = "ONCE_SUPPLIER_DELIVERY";
export const ONCE_SUPPLIER_INSTALL = "ONCE_SUPPLIER_INSTALL";
export const ONCE_SUPPLIER_ACCOUNT = "ONCE_SUPPLIER_ACCOUNT";

export default class OnceSupplierAction extends Action
{
    static switchDelivery(val: boolean){
        OnceSupplierAction.dispatch(ONCE_SUPPLIER_DELIVERY, val)
    }

    static switchInstall(val: boolean){
        OnceSupplierAction.dispatch(ONCE_SUPPLIER_INSTALL, val)
    }

    static switchAccount(val: boolean){
        OnceSupplierAction.dispatch(ONCE_SUPPLIER_ACCOUNT, val)
    }
}