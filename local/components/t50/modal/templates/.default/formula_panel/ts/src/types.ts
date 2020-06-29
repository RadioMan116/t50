export type Action = {type: string, payload: any}

export type PRICE_TYPE =  "rrc" | "free" | null
export type SUPPLIER =  {
    id: number,
    title: string
}
export type FORMULA_SIMPLE = {
    id: number,
    title: string,
    canDelete: boolean,
}
export type FORMULA = {
    id: number,
    title: string,
    date: string,
    manager: string
    comment: string
}
export type FORMULA_PARAMS_BLOCK = {
    min_commission?: number,
    max_commission?: number,
    min_purchase?: number,
    max_purchase?: number,
    percent?: number,
}

export type CITY = "MSK" | "SPB"

export type FORMULA_LOADED_DATA = {
    rrcSuppliers: number[],
    blocks: FORMULA_PARAMS_BLOCK[],
    current: FORMULA,
    calcFromSalePrice: boolean,
    priceType: PRICE_TYPE,
    check_rrc: boolean
}

export type FORMULA_UPDATE_DATA = {
    id?: number,
    title: string,
    city: CITY,
    comment: string,
    price_type: PRICE_TYPE,
    calc_from_sale: boolean,
    suppliers_calc_from_sale: number[],
    check_rrc: boolean,
    parameters: FORMULA_PARAMS_BLOCK[]
}