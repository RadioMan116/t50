import { FORMULA, FORMULA_SIMPLE, FORMULA_LOADED_DATA } from "@Root/types";
import { CHANGE_FORMULA, LOAD_FORMULAS, LOAD_FORMULA_FULL_DATA, RESET_FORMULA } from "@Root/consts";

export const changeFormula = (val: FORMULA) => ({
    type: CHANGE_FORMULA,
    payload: val
});

export const loadFormulas = (items: FORMULA_SIMPLE[]) => ({
    type: LOAD_FORMULAS,
    payload: items
});

export const loadFormulaFullData = (data: FORMULA_LOADED_DATA) => ({
    type: LOAD_FORMULA_FULL_DATA,
    payload: data
})

export const resetFormula = (deletedId: number) => ({
    type: RESET_FORMULA,
    payload: deletedId
})