import Action from "./Action";
import { SelectItem } from "@Root/components/Select";
import { DeductionItem } from "@Root/reducers/deduction";

export const DEDUCTION_LOAD_TYPES = "DEDUCTION_LOAD_TYPES";
export const DEDUCTION_LOAD = "DEDUCTION_LOAD";

export default class DeductionAction extends Action
{

    static async loadTypes(){
        let answer = await T50Ajax.postJson<SelectItem[]>("orders.detail", "deduction_load_types", {})
        if( answer.result ){
            DeductionAction.dispatch(DEDUCTION_LOAD_TYPES, answer.data)
        } else {
            T50Notify.error("не определены типы вычетов");
        }
    }

    static async load(order_id: number){
        let answer = await T50Ajax.postJson<DeductionItem[]>("orders.detail", "deduction_load", {order_id})
        if( answer.result ){
            DeductionAction.dispatch(DEDUCTION_LOAD, answer.data)
        } else {
            T50Notify.error("не загружены вычеты");
        }
    }

    static async create(order_id: number){
        let answer = await T50Ajax.postJson<DeductionItem[]>("orders.detail", "deduction_create", {order_id})
        Action.showMessage(answer, "Ошибка");
        if( answer.result )
            DeductionAction.dispatch(DEDUCTION_LOAD, answer.data)
    }

    static async change(order_id: number, id: number, code: keyof DeductionItem, value: number | string){
        let answer = await T50Ajax.postJson<DeductionItem[]>("orders.detail", "deduction_update", {order_id, id, code, value})
        Action.showMessage(answer, "Ошибка");
        if( answer.result )
            DeductionAction.dispatch(DEDUCTION_LOAD, answer.data)
    }

    static async delete(order_id: number, id: number){
        let answer = await T50Ajax.postJson<DeductionItem[]>("orders.detail", "deduction_delete", {order_id, id})
        Action.showMessage(answer, "Ошибка");
        if( answer.result )
            DeductionAction.dispatch(DEDUCTION_LOAD, answer.data)
    }
}