
import { Action, DocFile, ListItem, CommentItem } from "@Root/types";
import { SelectItem } from "@Root/components/Select";
import { RECLAMATION_LOAD_ALL, RECLAMATION_LOAD_FILES, RECLAMATION_LOAD_COMMENTS, RECLAMATION_LOAD_SCALAR, RECLAMATION_UPDATE_COMMENT } from "@Root/actions/ReclamationAction";
import Selectors from "@Root/tools/Selectors";

export type IReclamationScalar = {
    description: string,
    requirement: number,
    reason: number,
    result: number,
    error: number,
    date_request: string,
    date_finish: string,
    date_start: string,
    manager: number,
}

export type IReclamation = {
    files: DocFile[],
    requirements: SelectItem[],
    reasons: SelectItem[],
    results: SelectItem[],
    errors: SelectItem[],
    comments: CommentItem[],
} & IReclamationScalar

const initialState: IReclamation = {
    files: [],
    description: null,
    requirement: null,
    requirements: [],
    reason: null,
    reasons: [],
    error: null,
    errors: [],
    result: null,
    results: [],
    date_request: null,
    date_finish: null,
    date_start: null,
    manager: null,
    comments: [],
};

export default (state = initialState, action: Action) => {
    switch(action.type){
        case RECLAMATION_LOAD_ALL:
            return {...action.payload, comments: state.comments};
        case RECLAMATION_LOAD_FILES:
            return {...state, files: action.payload};
        case RECLAMATION_LOAD_COMMENTS:
            return {...state, comments: action.payload};
        case RECLAMATION_UPDATE_COMMENT:
            return {...state, comments: state.comments.map(comment => ( comment.id == action.payload.id ? action.payload : comment ))};
        case RECLAMATION_LOAD_SCALAR:
            return Selectors.updateStateExcept(action.payload, state, ["files", "requirements", "reasons", "results", "errors", "comments"])
    }
    return state;
}
