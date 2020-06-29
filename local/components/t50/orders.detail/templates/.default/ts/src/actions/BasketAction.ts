import Action from "./Action";
import { BasketItem, IBasket } from "@Root/reducers/basket";
import { ModalKey } from "@Root/types";
import APricesAction from "./APricesAction";
import CommonAction from "./CommonAction";


export const BASKET_LOAD_ALL = "BASKET_LOAD_ALL"

export default class BasketAction extends APricesAction
{
    static async loadAll(order_id: number){
        let answer = await T50Ajax.postJson<IBasket>("orders.detail", "basket_load", {order_id})
        if( !answer.result ){
            console.error(answer);
            T50Notify.error("ошибка при загрузке корзины");
            return;
        }
        BasketAction.dispatch(BASKET_LOAD_ALL, answer.data)
    }

    static async create(order_id: number, product_price_id: number, exchange_basket_id: number){
        let data = {order_id, product_price_id}
        if( exchange_basket_id > 0 )
            data["exchange_basket_id"] = exchange_basket_id
        let answer = await T50Ajax.postJson<{id: number}>("orders.detail", "basket_create", data)
        Action.showMessage(answer, "ошибка при обновлении корзины");
        if( answer.result )
            CommonAction.loadAllPrices()
    }

    static async setManual(order_id: number, id: number, price: number, comment: string, modal_key: ModalKey){
        let answer = await T50Ajax.postJson("orders.detail", "basket_set_manual", {order_id, id, price, comment, modal_key})
        Action.showMessage(answer, "ошибка при установке ручника");
        BasketAction.loadAll(order_id)
        if( answer.result )
            BasketAction.fastUpdate()
        return answer.result
    }

    static async delete(order_id: number, id: number){
        let answer = await T50Ajax.postJson("orders.detail", "basket_delete", {order_id, id})
        Action.showMessage(answer, "ошибка при удалении товара из корзины");
        if( answer.result )
            CommonAction.loadAllPrices()
    }

    static async setValue(order_id: number, id: number, code: keyof BasketItem, value: number, reloadAll = false){
        let answer = await T50Ajax.postJson("orders.detail", "basket_update", {order_id, id, code, value})
        Action.showMessage(answer, "ошибка при обновлении коризны");
        BasketAction.loadAll(order_id)
        if( answer.result ){
            ( reloadAll ?  CommonAction.loadAllPrices() : BasketAction.fastUpdate() )
        }
        return answer.result
    }

}