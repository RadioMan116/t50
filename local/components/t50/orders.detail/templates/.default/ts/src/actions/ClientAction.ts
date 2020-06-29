import Action from "./Action";
import { IClient } from "@Root/reducers/client";
import { store } from "@Root/Stor";
import { IOrderData } from "@Root/reducers/order";
import CommonAction, { ORDER_LOAD_DINAMIC } from "./CommonAction";

export const CLIENT_LOAD_DATA = "CLIENT_LOAD_DATA";
export const CLIENT_SEND_EMAIL = "CLIENT_SEND_EMAIL";

export default class ClientAction extends Action
{
    static async load(){
        let order_id = ClientAction.getOrderId()
        let answer = await T50Ajax.postJson<IClient>("orders.detail", "client_load", {order_id});
        if( !answer.result ){
            T50Notify.error("Клиент не найден")
            return
        }
        ClientAction.dispatch(CLIENT_LOAD_DATA, answer.data)
    }

    static async change(id: number, code: keyof IClient, value:string|boolean){
        let order_id = ClientAction.getOrderId()
        let answer = await T50Ajax.postJson<IClient>("orders.detail", "client_update", {id, code, value, order_id});

        if( answer.result ){
            T50Notify.success("обновлено")
            ClientAction.dispatch(CLIENT_LOAD_DATA, answer.data)
            Action.updateLogs()
        } else {
            T50Notify.error("ошибка при обновлении данных клиента")
            ClientAction.dispatch(CLIENT_LOAD_DATA, store.getState().client)
        }
    }

    static async setCity(cityId: number){
        let order_id = ClientAction.getOrderId()
        let answer = await T50Ajax.postJson<{client: IClient, order: IOrderData}>("orders.detail", "order_update", {order_id, code: "city", value: cityId});

        if( answer.result ){
            T50Notify.success("обновлено")
            ClientAction.dispatch(CLIENT_LOAD_DATA, answer.data.client);
            CommonAction.dispatch(ORDER_LOAD_DINAMIC, answer.data.order);
            Action.updateLogs()
        } else {
            T50Notify.error("ошибка при обновлении данных клиента")
            ClientAction.dispatch(CLIENT_LOAD_DATA, store.getState().client)
        }
    }
}