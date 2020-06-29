import { Action, SUPPLIER } from "@Root/types";
import { LOAD_SUPPLIERS, CHANGE_RRC_SUPPLIERS } from "@Root/consts";

export interface ISuppliers
{
    items: SUPPLIER[],
    rrcSuppliers: number[]
}

const initialState: ISuppliers = {
    items: [
        // {id: 0, title: "Техпорт"},
        // {id: 1, title: "ВстройкаСоло"},
        // {id: 2, title: "Headway"},
        // {id: 3, title: "Vertex"},
        // {id: 4, title: "ЦВТ"},
        // {id: 5, title: "Техностор"},
        // {id: 6, title: "Falmec"},
        // {id: 7, title: "Homaer"},
        // {id: 8, title: "ПаркБТ"},
        // {id: 9, title: "МегаЛекс"},
    ],
    rrcSuppliers: []
};

export default (state = initialState, action: Action) => {
    switch(action.type){
        case LOAD_SUPPLIERS:
            return {...state, items: action.payload}
        case CHANGE_RRC_SUPPLIERS:
            return {...state, rrcSuppliers: action.payload}
    }
    return state;
}
