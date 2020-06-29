import { IRootReducer } from "./reducer";

export const LOADL_ALL = "LOADL_ALL"
export const CHANGE_DATE = "CHANGE_DATE"
export const CHANGE_COMMENT = "CHANGE_COMMENT"
export const CHANGE_VALUE = "CHANGE_VALUE"


export const loadAll = (data: IRootReducer) => ({
    type: LOADL_ALL,
    payload: data
})

export const changeDate = (date: string) => ({
    type: CHANGE_DATE,
    payload: date
})

export const changeComment = (comment: string) => ({
    type: CHANGE_COMMENT,
    payload: comment
})

export const changeValue = (value: number) => ({
    type: CHANGE_VALUE,
    payload: value
})