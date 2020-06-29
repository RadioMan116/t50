import { IRootReducer } from "./reducer";
import { store } from "./Stor";
import { SelectItem } from "./components/Select";

export const INITIAL_LOAD = "INITIAL_LOAD"
export const LOAD_SERVICES = "LOAD_SERVICES"
export const LOAD_SERVICE = "LOAD_SERVICE"
export const LOAD_ALL = "LOAD_ALL"
export const SET_CUSTOM_INPUT = "SET_CUSTOM_INPUT"
export const RESET_ALL = "RESET_ALL"


export default class Action {
    static reset(){
        store.dispatch({type: RESET_ALL, payload: null});
    }

    static setCustomInput(text: string){
        store.dispatch({type: SET_CUSTOM_INPUT, payload: text});
    }

    static async initial(provider?: string) {
        let data = await Action.loal<{providers: SelectItem[], provider: string, categories: SelectItem[]}>(
            "installation_prices_load_categories", ( provider == null ? {} : {provider} ), "Ошибка при загрузке категорий");
        if( data != null )
            store.dispatch({type: INITIAL_LOAD, payload: data});
    }

    static async selectCategoty(provider: string, category_id: number) {
        let data = await Action.loal<{category_id: number, services: SelectItem[]}>(
            "installation_prices_load_services", {provider, category_id}, "Ошибка при выборе категории");
        if( data != null )
        store.dispatch({type: LOAD_SERVICES, payload: data});
    }

    static async selectService(provider: string, category_id: number, service_id: number) {
        let data = await Action.loal<{service_id: number, price: number}>(
            "installation_prices_load_service", {provider, category_id, service_id}, "Ошибка при загрузке сервиса");
        if( data != null )
        store.dispatch({type: LOAD_SERVICE, payload: data});
    }


    static async loadService(provider: string, service_id: number) {
        let data = await Action.loal<IRootReducer>(
            "installation_prices_load_service", {provider, service_id, all: "Y"}, "Ошибка при загрузке сервиса");
        if( data != null )
            store.dispatch({type: LOAD_ALL, payload: data});
    }

    private static async loal<T>(action: string, params = {}, errorMsg: string): Promise<T>{
        let answer = await T50Ajax.postJson<T>("orders.detail", action, params);
        if( answer.result == false ){
            T50Notify.error(errorMsg);
            return null;
        }

        return answer.data;
    }
}