import { store } from "./Stor";
import { CITY, FORMULA_LOADED_DATA, SUPPLIER, FORMULA_SIMPLE, FORMULA_UPDATE_DATA } from "./types";
import { loadFormulas, loadFormulaFullData, resetFormula } from "./actions/formulasActions";
import { loadSuppliers } from "./actions/suppliersActions";
import { changeLoadingStatus, addLog, changeCity } from "./actions/commonActions";

export default class API {
    static async loadInfo(city: CITY) {
        let result = await API.post<{ formulas: FORMULA_SIMPLE[], suppliers: SUPPLIER[] }>("load_formulas_info", { city });
        store.dispatch(loadFormulas(result.data.formulas))
        store.dispatch(loadSuppliers(result.data.suppliers))
        // addLog("Загружены поставщики и формулы")
        API.changeLoadingStatus()
    }

    static async loadFormula(id: number, city: CITY = "MSK") {
        let result = await API.post<FORMULA_LOADED_DATA>("load_formula", { id, city });

        store.dispatch(loadFormulaFullData(result.data));

        API.changeLoadingStatus()
    }

    static async deleteFormula(id: number) {
        let answer = await API.post("delete_formula", { id });

        if (answer.result) {
            store.dispatch(resetFormula(id))
            API.loadInfo(store.getState().common.city)
        }

        API.changeLoadingStatus()
        return answer
    }

    static async updateFormula(data: FORMULA_UPDATE_DATA) {
        let answer = await API.post<number>("update_formula", data);
        API.changeLoadingStatus()
        return answer
    }

    static async setFromulaForProducts(prices_id: number[], formula_id: number){
        let answer = await API.post<number[]>("set_formula_for_products", {prices_id, formula_id});
        API.changeLoadingStatus()
        return answer;
    }

    static async recaclProducts(unids: number[]){
        let answer = await API.post("recalc_products", {unids});
        API.changeLoadingStatus()
        return answer.result;
    }

    static async getUnidsByFormula(formula_id: number){
        let answer = await API.post<number[]>("get_unids_by_formula", {formula_id});
        API.changeLoadingStatus()
        return answer;
    }

    private static post<T>(action: string, data = {}): Promise<T50AjaxResult<T>> {
        API.changeLoadingStatus(true)
        return T50Ajax.postJson<T>("catalog.element", action, data);
    }

    private static changeLoadingStatus(on = false){
        store.dispatch(changeLoadingStatus(on));
    }
}