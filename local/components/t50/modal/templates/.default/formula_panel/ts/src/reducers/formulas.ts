import { Action, FORMULA, FORMULA_SIMPLE } from "@Root/types";
import { LOAD_FORMULAS, CHANGE_FORMULA } from "@Root/consts";

export interface IFormulas
{
    items: FORMULA_SIMPLE[],
    current: FORMULA,
}

const initialState: IFormulas = {
    items: [
        // {id: 1, title: "Gorenje_хол_2000(+7%)6000 1", date: "12.12.2012", manager: "менеджер 1"},
        // {id: 2, title: "Gorenje_хол_2000(+7%)6000 2", date: "07.12.2019", manager: "менеджер 2"},
        // {id: 3, title: "Gorenje_хол_2000(+7%)6000 3", date: "01.05.2020", manager: "менеджер 3"},
    ],
    current: null,
};

export default (state = initialState, action: Action) => {
    switch(action.type){
        case LOAD_FORMULAS:
            return {...state, items: action.payload}

        case CHANGE_FORMULA:
            return {...state, current: action.payload}
    }
    return state;
}
