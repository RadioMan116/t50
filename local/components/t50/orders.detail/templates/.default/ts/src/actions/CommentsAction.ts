import Action from "./Action";
import { ICommentsFilter } from "@Root/reducers/comments";
import { SelectItem } from "@Root/components/Select";
import { CommentItem } from "@Root/types";

export const COMMENTS_ALL_DATA = "COMMENTS_ALL_DATA";
export const COMMENTS_UPDATE = "COMMENTS_UPDATE";
export const COMMENTS_CHANGE_FILTER = "COMMENTS_CHANGE_FILTER";
export const COMMENTS_RESET_FILTER = "COMMENTS_RESET_FILTER";

export type DataCommentModal = {
    remind: boolean,
    remind_date: string,
    remind_time: string,
    theme: number,
    comment: string,
    targetManagers: number[]
}

export default class CommentsAction extends Action
{
    static async loadByFilter(filter: ICommentsFilter){
        if( filter == null )
            filter = {}
        let order_id = CommentsAction.getOrderId()
        let answer = await T50Ajax.postJson<{items: CommentItem[], themes: SelectItem[]}>("orders.detail", "comments_load", {...filter, order_id});
        CommentsAction.dispatch(COMMENTS_ALL_DATA, answer.data)
    }

    static changeFilter(key: keyof ICommentsFilter, value: number | string){
        let updObj = {}
        updObj[key] = value
        CommentsAction.dispatch(COMMENTS_CHANGE_FILTER, updObj)
    }

    static resetFilter(){
        let filter = {
            date_from: null,
            date_to: null,
            manager: null,
            theme: null
        }
        CommentsAction.dispatch(COMMENTS_RESET_FILTER, filter)
        CommentsAction.loadByFilter(filter)
    }

    static async addComment(data: DataCommentModal){
        let order_id = CommentsAction.getOrderId()
        let answer = await T50Ajax.postJson<{items: CommentItem[], themes: SelectItem[]}>("orders.detail", "comments_create", {...data, order_id});
        Action.showMessage(answer, "ошибка при добавлении комменатрия")
        if( answer.result )
            CommentsAction.loadByFilter(Action.getState().comments.filter)
        return answer.result
    }

    static async changeItemRemindData(id: number, data?: {remind_date?: string, remind_time?: string, unset?: boolean}){
        let order_id = CommentsAction.getOrderId()
        if( data == null )
            data = {unset: true}

        let answer = await T50Ajax.postJson<CommentItem>("orders.detail", "comments_update", {order_id, id, ...data});
        Action.showMessage(answer, "ошибка при обновлении комменатрия")
        if( answer.result )
            CommentsAction.dispatch(COMMENTS_UPDATE, answer.data)
        return answer.result
    }
}