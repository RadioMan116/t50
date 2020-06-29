import { MONTHS } from "./consts"

export type Action = {type: string, payload: any}
export type ListItem = {id: number, title: string}
// export type Shop = {id: number, title: string}
export type Manager = {id: number, name: string}
export type OrderStatus = {id: number, title: string}

export type DocFile = {id: number, title: string, path: string, can_delete: boolean}

export type OrderId = {order_id: number}

export type Month = keyof typeof MONTHS

export type PriceComment = {comment: string, manager: string, before: number, after: number, date: string}
export type PriceComments = {[code in "sale" | "purchase"]?: PriceComment}

export type ModalKey = "basket_sale" | "basket_purchase" | "install_sale" | "install_purchase"
export type ModalData = {
    bind_id: number,
    comment: string,
    modal_key: ModalKey,
    value: number
}


export type CommentItem = {
    id: number,
    manager: string,
    target_managers: number[],
    date: string,
    message: string,
    theme: number,
    remind: boolean,
    remind_date?: string,
    remind_time?: string,
}

export type InstallationPriceData = {
    provider: string,
    category_id: number,
    service_id: number,
    installation_id: number,
    price: number,
    custom_input?: string
}