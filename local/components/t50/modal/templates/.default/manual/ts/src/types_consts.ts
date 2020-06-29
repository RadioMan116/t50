export const VALID_KEYS = <const> ["avail_supplier","avail_shop", "purchase", "sale",   "basket_sale", "basket_purchase", "install_sale", "install_purchase"]
export type Action = {type: string, payload: any}
export type ModalKey = typeof VALID_KEYS[number]