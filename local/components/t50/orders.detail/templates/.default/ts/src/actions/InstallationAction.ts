import { InstallationItem, IInstallation } from "@Root/reducers/installation";
import { InstallationPriceData } from "@Root/types";
import APricesAction from "./APricesAction";
import CommonTools from "@Root/tools/CommonTools";

export const INSTALLATION_LOAD_ALL = "INSTALLATION_LOAD_ALL"

export default class InstallationAction extends APricesAction
{
    static async loadAll(){
        let order_id = this.getOrderId()
        let answer = await T50Ajax.postJson<IInstallation>("orders.detail", "installation_load", {order_id})
        if( !answer.result ){
            console.error(answer);
            T50Notify.error("ошибка при загрузке данных по установке");
            return;
        }
        InstallationAction.dispatch(INSTALLATION_LOAD_ALL, answer.data)
    }

    static async setValue(basket_id: number, id: number, code: keyof InstallationItem, value: string|number){
        let data = {basket_id, id, code, value}
        return this.update(data);
    }

    static async setService(basket_id: number, id: number, service: InstallationPriceData){
        let data = {basket_id, id, code: "service", provider: service.provider, value : service.service_id};
        return this.update(data);
    }

    private static async update(updData: {}){
        let order_id = this.getOrderId()
        Object.assign(updData, {order_id, ...CommonTools.getMultipleUpdateFlag("install", updData["code"])});

        let answer = await T50Ajax.postJson<IInstallation>("orders.detail", "installation_update", updData)
        if( answer.result ){
            T50Notify.success("обновлено")
            InstallationAction.loadAll()
            InstallationAction.fastUpdate()
        } else {
            T50Notify.error("ошибка при обновлении данных по установке")
            InstallationAction.dispatch(INSTALLATION_LOAD_ALL, InstallationAction.getState().installation)
        }
        return answer.result
    }

    static async create(service: InstallationPriceData){
        let order_id = this.getOrderId()
        let {provider, service_id} = service
        let answer = await T50Ajax.postJson<IInstallation>("orders.detail", "installation_create", {order_id, provider, service_id, product_name: service.custom_input})
        if( answer.result ){
            InstallationAction.dispatch(INSTALLATION_LOAD_ALL, answer.data)
            InstallationAction.fastUpdate()
        } else {
            T50Notify.error("Ошибка при создании заявки на установку");
        }
        return answer.result
    }

    static async delete(id: number){
        let order_id = this.getOrderId()
        let answer = await T50Ajax.postJson<IInstallation>("orders.detail", "installation_delete", {order_id, id})
        if( answer.result ){
            InstallationAction.dispatch(INSTALLATION_LOAD_ALL, answer.data)
            InstallationAction.fastUpdate()
        } else {
            T50Notify.error("Ошибка при удалении заявки на установку");
        }
        return answer.result
    }

    static async sentToRemcity(){
        let order_id = this.getOrderId()
        let answer = await T50Ajax.postJson<IInstallation>("orders.detail", "sent_remcity", {order_id})
        if( answer.result ){
            InstallationAction.dispatch(INSTALLATION_LOAD_ALL, answer.data)
        } else {
            T50Notify.error("Ошибка при отправке заявки в remcity.net");
        }
        return answer.result
    }
}