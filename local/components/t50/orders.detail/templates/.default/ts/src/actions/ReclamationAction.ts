import Action from "./Action";
import { IReclamation, IReclamationScalar } from "@Root/reducers/reclamation";
import { DocFile, CommentItem } from "@Root/types";
import { DataCommentModal } from "./CommentsAction";

export const RECLAMATION_LOAD_ALL = "RECLAMATION_LOAD_ALL";
export const RECLAMATION_LOAD_FILES = "RECLAMATION_LOAD_FILES";
export const RECLAMATION_LOAD_COMMENTS = "RECLAMATION_LOAD_COMMENTS";
export const RECLAMATION_UPDATE_COMMENT = "RECLAMATION_UPDATE_COMMENT";
export const RECLAMATION_LOAD_SCALAR = "RECLAMATION_LOAD_SCALAR";

export default class ReclamationAction extends Action
{
    static async loadAll(){
        let order_id = ReclamationAction.getOrderId();
        let answer = await T50Ajax.postJson<IReclamation>("orders.detail", "reclamation_load_all", {order_id})
        if( answer.result ){
            ReclamationAction.dispatch(RECLAMATION_LOAD_ALL, answer.data)
        } else {
            T50Notify.error("Не удалось загрузить данные по рекламации.");
        }
    }

    static async update(code: keyof IReclamationScalar, value: string | number){
        let order_id = ReclamationAction.getOrderId();
        let answer = await T50Ajax.postJson<IReclamationScalar>("orders.detail", "reclamation_update", {order_id, code, value})
        ReclamationAction.showMessage(answer, "Ошибка");
        if( answer.result ){
            ReclamationAction.dispatch(RECLAMATION_LOAD_SCALAR, answer.data)
        }
    }

    static async saveFile(formdata: FormData){
        formdata.append("order_id", ReclamationAction.getOrderId().toString());
        let answer = await T50Ajax.postFormData<DocFile[]>("orders.detail", "reclamation_save_file", formdata)
        if( answer.result ){
            ReclamationAction.dispatch(RECLAMATION_LOAD_FILES, answer.data)
        } else {
            T50Notify.error("Ошибка при загрузке файла")
        }
    }

    static async removeFile(file_id: number){
        let order_id = ReclamationAction.getOrderId();
        let answer = await T50Ajax.postJson<DocFile[]>("orders.detail", "reclamation_delete_file", {file_id, order_id})
        if( answer.result ){
            ReclamationAction.dispatch(RECLAMATION_LOAD_FILES, answer.data)
        } else {
            T50Notify.error("Ошибка при удалении файла")
        }
    }

    static async loadComments(){
        let order_id = ReclamationAction.getOrderId();
        let answer = await T50Ajax.postJson<{items: CommentItem[]}>("orders.detail", "comments_load", {order_id, reclamation: true});
        ReclamationAction.dispatch(RECLAMATION_LOAD_COMMENTS, answer.data.items)
    }

    static async addComment(data: DataCommentModal){
        let order_id = ReclamationAction.getOrderId();
        let answer = await T50Ajax.postJson<{items: CommentItem[]}>("orders.detail", "comments_create", {...data, order_id, reclamation: true});
        Action.showMessage(answer, "Ошибка при обновлении хронологии")
        if( answer.result )
            ReclamationAction.dispatch(RECLAMATION_LOAD_COMMENTS, answer.data.items)
        return answer.result
    }

    static async changeCommentItemRemindData(id: number, data?: {remind_date?: string, remind_time?: string, unset?: boolean}){
        let order_id = ReclamationAction.getOrderId();
        if( data == null )
            data = {unset: true}

        let answer = await T50Ajax.postJson<CommentItem>("orders.detail", "comments_update", {order_id, id, ...data, reclamation: true});
        Action.showMessage(answer, "Ошибка при обновлении хронологии")
        if( answer.result )
            ReclamationAction.dispatch(RECLAMATION_UPDATE_COMMENT, answer.data)
        return answer.result
    }
}