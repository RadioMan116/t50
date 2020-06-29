

import { Action, CommentItem } from "@Root/types";
import { SelectItem } from "@Root/components/Select";
import { COMMENTS_ALL_DATA, COMMENTS_RESET_FILTER, COMMENTS_CHANGE_FILTER, COMMENTS_UPDATE } from "@Root/actions/CommentsAction";


export type ICommentsFilter = {
    date_from?: string,
    date_to?: string,
    manager?: number,
    theme?: number
}
export interface IComments
{
    items: CommentItem[],
    themes: SelectItem[],
    filter: ICommentsFilter
}

const initialState: IComments = {
    items: [],
    themes: [],
    filter: {
        date_from: null,
        date_to: null,
        manager: null,
        theme: null
    }
};

export default (state = initialState, action: Action) => {
    switch(action.type){
        case COMMENTS_ALL_DATA:
            return {...state, items: action.payload.items, themes: action.payload.themes}

        case COMMENTS_UPDATE:
            return {...state, items: state.items.map(comment => {
                return ( comment.id == action.payload.id ? action.payload : comment );
            })}

        case COMMENTS_RESET_FILTER:
            return {...state, filter: action.payload}

        case COMMENTS_CHANGE_FILTER:
            var filter = {...state.filter}
            Object.assign(filter, action.payload)
            return {...state, filter}

    }
    return state;
}
