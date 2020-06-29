import { store } from "@Root/Stor";
import Action from "./Action";
import { BasketDeliveryItem, IDelivery } from "@Root/reducers/delivery";
import APricesAction from "./APricesAction";
import CommonTools from "@Root/tools/CommonTools";

export const DELIVERY_LOAD_ALL = "DELIVERY_LOAD_ALL"

export default class DeliveryAction extends APricesAction
{
    static async loadAllByOrderId(order_id: number){
        let answer = await T50Ajax.postJson<IDelivery>("orders.detail", "delivery_load", {order_id})
        if( !answer.result ){
            T50Notify.error("Не найдены данные по доставке")
        } else {
            DeliveryAction.dispatch(DELIVERY_LOAD_ALL, answer.data)
        }
    }

    static async setValue(order_id: number, basket_id: number, code: keyof BasketDeliveryItem, value: string|number){
        let answer = await T50Ajax.postJson<IDelivery>("orders.detail", "delivery_update", {order_id, basket_id, code, value, ...CommonTools.getMultipleUpdateFlag("delivery", code)})
        if( answer.result ){
            DeliveryAction.dispatch(DELIVERY_LOAD_ALL, answer.data)
            T50Notify.success("обновлено")
            DeliveryAction.fastUpdate()
        } else {
            T50Notify.error("ошибка при обновлении данных по доставке")
            DeliveryAction.dispatch(DELIVERY_LOAD_ALL, DeliveryAction.getState().delivery)
        }
    }
}