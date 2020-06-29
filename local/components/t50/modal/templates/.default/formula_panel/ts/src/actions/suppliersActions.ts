import { LOAD_SUPPLIERS, CHANGE_RRC_SUPPLIERS } from "@Root/consts";
import { SUPPLIER } from "@Root/types";


export const loadSuppliers = (items: SUPPLIER[]) => ({
    type: LOAD_SUPPLIERS,
    payload: items
});

export const changeRRCSuppliers = (items: number[]) => ({
    type: CHANGE_RRC_SUPPLIERS,
    payload: items
});