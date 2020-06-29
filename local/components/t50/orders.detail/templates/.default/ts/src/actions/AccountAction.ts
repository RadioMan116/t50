import Action from "./Action";
import { AccountsItem, IAccounts } from "@Root/reducers/accounts";
import BasketAction from "./BasketAction";
import CommonTools from "@Root/tools/CommonTools";
import CommonAction from "./CommonAction";

export const ACCOUNT_LOAD_ITEMS = "ACCOUNT_LOAD_ITEMS"
export const ACCOUNT_LOAD_ITEM = "ACCOUNT_LOAD_ITEM"

export default class AccountAction extends Action
{
    static async loadAll(){
        let order_id = Action.getOrderId();
        let answer = await T50Ajax.postJson<IAccounts>("orders.detail", "account_load", {order_id})
        if( !answer.result ){
            T50Notify.error("Не найдены номера счетов и заказов")
        } else {
            AccountAction.dispatch(ACCOUNT_LOAD_ITEMS, answer.data.items)
        }
    }

    static async setValue(order_id: number, basket_id: number, code: keyof AccountsItem, value: string|string[]|boolean){
        let answer = await T50Ajax.postJson<IAccounts>("orders.detail", "account_update", {order_id, basket_id, code, value, ...CommonTools.getMultipleUpdateFlag("accounts", code)})
        if( answer.result ){
            T50Notify.success("обновлено")
            Action.updateLogs()
        } else {
            T50Notify.error("ошибка при обновлении данных по счетам")
        }
        if( CommonTools.getMultipleUpdateFlag("accounts", code).multiple_update_mode ){
            AccountAction.loadAll()
        } else {
            AccountAction.dispatch(ACCOUNT_LOAD_ITEM, answer.data)
        }
    }

    static async removeRow(order_id: number, basket_id: number, index: number){
        let answer = await T50Ajax.postJson<IAccounts>("orders.detail", "account_remove_row", {order_id, basket_id, index})
        if( answer.result ){
            T50Notify.success("обновлено")
            Action.updateLogs()
        } else {
            T50Notify.error("ошибка при удалении строки со счетами")
        }
        AccountAction.dispatch(ACCOUNT_LOAD_ITEM, answer.data)
    }

    static async setSupplier(order_id: number, basket_id: number, supplier: string|number){
        let answerBasket = await T50Ajax.postJson("orders.detail", "basket_update", {order_id, id: basket_id, code: "supplier_id", value: supplier, ...CommonTools.getMultipleUpdateFlag("accounts", "")})
        if( answerBasket.result ){
            T50Notify.success("обновлено")
            await BasketAction.loadAll(order_id)
            CommonAction.loadAllPrices()
            Action.updateLogs()
        } else {
            T50Notify.error("ошибка при обновлении поставщика")
        }
    }
}