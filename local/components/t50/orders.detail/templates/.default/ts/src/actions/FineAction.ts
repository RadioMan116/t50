import Action from "./Action";
import { FineItem } from "@Root/reducers/fine";

export const FINE_LOAD_ALL = "FINE_LOAD_ALL";

export default class FineAction extends Action
{
    static async load(order_id: number){
        let answer = await T50Ajax.postJson<FineItem[]>("orders.detail", "fine_load", {order_id})
        if( answer.result ){
            FineAction.dispatch(FINE_LOAD_ALL, answer.data)
        } else {
            T50Notify.error("Не удалось загрузить данные по штрафам.");
        }
    }

    static async create(order_id: number){
        let answer = await T50Ajax.postJson<FineItem[]>("orders.detail", "fine_create", {order_id})
        Action.showMessage(answer, "Ошибка");
        if( answer.result )
            FineAction.dispatch(FINE_LOAD_ALL, answer.data)
    }

    static async change(order_id: number, id: number, code: keyof FineItem, value: number | string){
        let answer = await T50Ajax.postJson<FineItem[]>("orders.detail", "fine_update", {order_id, id, code, value})
        Action.showMessage(answer, "Ошибка");
        if( answer.result )
            FineAction.dispatch(FINE_LOAD_ALL, answer.data)
    }

    static async delete(order_id: number, id: number){
        let answer = await T50Ajax.postJson<FineItem>("orders.detail", "fine_delete", {order_id, id})
        Action.showMessage(answer, "Ошибка");
        if( answer.result )
            FineAction.dispatch(FINE_LOAD_ALL, answer.data)
    }
}