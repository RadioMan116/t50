import Action from "./Action";
import { IProfit, ProfitBasketSum, ProfitBasketItem, ProfitInstalItem } from "@Root/reducers/profit";
import { Month } from "@Root/types";
import { store } from "@Root/Stor";
import { ORDER_CHANGE_SHARE_MANAGER } from "./CommonAction";

export const PROFIT_LOAD_ALL = "PROFIT_LOAD_ALL"
export type AccountMonth = "month_vp" | "month_zp"

export default class ProfitAction extends Action {
    static async loadByOrderId() {
        let order_id = store.getState().order.order_id
        let answer = await T50Ajax.postJson<IProfit>("orders.detail", "load_profit", {order_id})
        if( !answer.result ){
            console.error(answer);
            T50Notify.error("ошибка при загрузке сводной таблицы");
            return;
        }
        ProfitAction.dispatch(PROFIT_LOAD_ALL, answer.data)

    }

    static async setBasketSupplierCommission(item: ProfitBasketItem, value: number) {
        let order_id = store.getState().order.order_id
        let answer = await T50Ajax.postJson("orders.detail", "basket_update", {order_id, id: item.id, code: "suppl_commission", value})
        Action.showMessage(answer, "ошибка при изменении коммиссии поставщика");
        ProfitAction.loadByOrderId();
        return answer.result
    }

    static async setInstalSupplierCommission(item:ProfitInstalItem, value: number) {
        let order_id = store.getState().order.order_id
        let answer = await T50Ajax.postJson("orders.detail", "installation_update", {order_id, id: item.id, code: "suppl_commission", value})
        Action.showMessage(answer, "ошибка при изменении коммиссии поставщика (установка)");
        ProfitAction.loadByOrderId();
        return answer.result
    }

    static async setBasketAccountMonth(code: AccountMonth, item: ProfitBasketItem, value: Month) {
        let order_id = store.getState().order.order_id
        let answer = await T50Ajax.postJson("orders.detail", "basket_update", {order_id, id: item.id, code, value})
        Action.showMessage(answer, "ошибка при изменении отчетного месяца");
        ProfitAction.loadByOrderId();
        return answer.result
    }

    static async setInstalAccountMonth(code: AccountMonth, item: ProfitInstalItem, value: Month) {
        let order_id = store.getState().order.order_id
        let answer = await T50Ajax.postJson("orders.detail", "installation_update", {order_id, id: item.id, code, value})
        Action.showMessage(answer, "ошибка при изменении отчетного месяца");
        ProfitAction.loadByOrderId();
        return answer.result
    }

    static async setShareManager(managerId: number){
        let order_id = store.getState().order.order_id
        let answer = await T50Ajax.postJson("orders.detail", "order_update", {order_id, code: "share_com_manager", value: managerId})
        Action.showMessage(answer, "ошибка при распределении комиссии");
        if( answer.result )
            ProfitAction.dispatch(ORDER_CHANGE_SHARE_MANAGER, managerId)
    }

}