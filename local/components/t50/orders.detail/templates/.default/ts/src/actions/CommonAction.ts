import Action from "./Action";
import { IOrderData } from "@Root/reducers/order";
import ClientAction from "./ClientAction";
import { IBasket } from "@Root/reducers/basket";
import { IDelivery } from "@Root/reducers/delivery";
import { IAccounts } from "@Root/reducers/accounts";
import { IInstallation } from "@Root/reducers/installation";
import AccountAction, { ACCOUNT_LOAD_ITEMS } from "./AccountAction";
import BasketAction, { BASKET_LOAD_ALL } from "./BasketAction";
import DeliveryAction, { DELIVERY_LOAD_ALL } from "./DeliveryAction";
import InstallationAction, { INSTALLATION_LOAD_ALL } from "./InstallationAction";
import { IProfit } from "@Root/reducers/profit";
import ProfitAction, { PROFIT_LOAD_ALL } from "./ProfitAction";

export const ORDER_LOAD_STATISTIC = "ORDER_LOAD_STATISTIC";
export const ORDER_LOAD_DINAMIC = "ORDER_LOAD_DINAMIC";
export const ORDER_CHANGE_SHARE_MANAGER = "ORDER_CHANGE_SHARE_MANAGER";

type AllPrices = {
    ORDER: IOrderData,
    BASKET: IBasket,
    DELIVERY: IDelivery,
    ACCOUNT: IAccounts,
    INSTALLATION: IInstallation,
    PROFIT: IProfit,
}
export default class CommonAction extends Action {

    static async loadOrder() {
        let order_id = Action.getState().order.order_id;

        let answer = await T50Ajax.postJson<IOrderData>("orders.detail", "order_load", { order_id })
        if (!answer.result) {
            T50Notify.error("Ошибка")
            return false;
        }

        CommonAction.dispatch(ORDER_LOAD_DINAMIC, answer.data)

        return answer.result;
    }

    static async loadAllPrices() {
        let order_id = Action.getState().order.order_id;

        let answer = await T50Ajax.postJson<AllPrices>("orders.detail", "load_all_prices", { order_id, load_all: true })
        if (!answer.result) {
            T50Notify.error("Ошибка")
            return false;
        }

        BasketAction.dispatch(BASKET_LOAD_ALL, answer.data.BASKET)
        AccountAction.dispatch(ACCOUNT_LOAD_ITEMS, answer.data.ACCOUNT.items)
        DeliveryAction.dispatch(DELIVERY_LOAD_ALL, answer.data.DELIVERY)
        InstallationAction.dispatch(INSTALLATION_LOAD_ALL, answer.data.INSTALLATION)
        ProfitAction.dispatch(PROFIT_LOAD_ALL, answer.data.PROFIT)

        return answer.result;
    }

    static async loadStatic() {
        let order_id = Action.getState().order.order_id;

        let answer = await T50Ajax.postJson<IOrderData>("orders.detail", "load_static", { order_id })
        if (!answer.result) {
            T50Notify.error("Невозможно загрузить заказ")
            return false;
        }

        CommonAction.dispatch(ORDER_LOAD_STATISTIC, answer.data)
        ClientAction.load()
        return true;
    }

    static getOrderId() {
        let match = document.location.pathname.match(/\/orders\/(\d+)\//)
        if (match == null)
            return 0

        let id = parseInt(match[1])
        if (isNaN(id))
            return 0

        return id
    }

    static async setValue(code: keyof IOrderData, value: number|string) {
        let order_id = Action.getState().order.order_id;
        let answer = await T50Ajax.postJson<IOrderData>("orders.detail", "order_update", { order_id, code, value })
        CommonAction.showMessage(answer, "ошибка")
        if (answer.result) {
            CommonAction.dispatch(ORDER_LOAD_DINAMIC, answer.data)
            CommonAction.updateLogs()
        }
        return answer.result
    }

}
