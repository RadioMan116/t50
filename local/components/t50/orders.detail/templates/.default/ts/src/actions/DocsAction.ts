import Action from "./Action";
import { IDocs } from "@Root/reducers/docs";
import { DocFile } from "@Root/types";

export const DOCS_LOAD_ALL = "DOCS_LOAD_ALL";
export const DOCS_LOAD_BY_TYPE = "DOCS_LOAD_BY_TYPE";


// let path = "/upload/orders_files/document.xlsx";
// let data:IDocs = {
//     company_card: [{id:1, title: "file1.xlsx", path}, {id:2, title: "other_doc.xlsx", path}, {id:2, title: "Карточка предприятия.xlsx", path}],
//     our_prepayment_invoice: [{id:1, title: "кириллица.jpg", path}, {id:2, title: "чет на предоплату наш.xlsx", path}],
//     partners_prepayment_invoice: [{id:1, title: "для партнеров.docx", path}],
//     proxy_shipment_tk: [{id:1, title: "Доверенность в ТК.jpeg", path}],
//     contract: [{id:1, title: "Договор.excel", path}],
//     purchase_order: [{id:1, title: "Заказ-наряд.excel", path}],
//     proxy_receipt_goods: [{id:1, title: "Доверенность груз.jpeg", path}],
// };

export default class DocsAction extends Action
{
    static async loadAll(order_id: number){
        let answer = await T50Ajax.postJson<IDocs>("orders.detail", "docs_load", {order_id})
        if( answer.result ){
            DocsAction.dispatch(DOCS_LOAD_ALL, answer.data)
        } else {
            T50Notify.error("Ошибка при поиске документов")
        }
    }

    static async loadByType(order_id: number, type: keyof IDocs){
        let answer = await T50Ajax.postJson<DocFile[]>("orders.detail", "docs_load", {order_id, type})
        if( answer.result ){
            DocsAction.dispatchByType(type, answer.data)
        } else {
            T50Notify.error("Ошибка при поиске документов")
        }
    }

    static async submitFormData(formdata: FormData){
        let answer = await T50Ajax.postFormData<DocFile[]>("orders.detail", "upload_doc", formdata)
        if( answer.result ){
            let type = formdata.get("type") as keyof IDocs
            DocsAction.dispatchByType(type, answer.data)
        } else {
            T50Notify.error("Ошибка при загрузке документа")
        }
    }

    static async removeFileById(file_id: number, order_id: number, type: keyof IDocs){
        let answer = await T50Ajax.postJson<DocFile[]>("orders.detail", "delete_doc", {file_id, order_id, type})
        if( answer.result ){
            DocsAction.dispatchByType(type, answer.data)
        } else {
            T50Notify.error("Ошибка при удалении документа")
        }
    }

    private static dispatchByType(type: keyof IDocs, data: DocFile[]){
        let dataByType = {}
        dataByType[type] = data
        DocsAction.dispatch(DOCS_LOAD_BY_TYPE, dataByType)
    }
}