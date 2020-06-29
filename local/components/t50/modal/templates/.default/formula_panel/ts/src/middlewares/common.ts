import { Store } from "redux";
import { IRootReducer } from "@Root/reducers";
import { Action, FORMULA_LOADED_DATA } from "@Root/types";
import { LOAD_FORMULA_FULL_DATA, RESET_FORMULA, MAKE_NEW_EMPTY_FORMULA } from "@Root/consts";
import { changeFormula } from "@Root/actions/formulasActions";
import { changeParameters, changeCheckRRC } from "@Root/actions/parametersActions";
import { changeCalcFromSalePrice, changePriceType, addLog } from "@Root/actions/commonActions";
import { changeRRCSuppliers } from "@Root/actions/suppliersActions";

export const common = (store: Store<IRootReducer>) => next => (action: Action) => {

    if( action.type == LOAD_FORMULA_FULL_DATA ){
        let data = action.payload as FORMULA_LOADED_DATA;
        store.dispatch(changeFormula(data.current))
        store.dispatch(changeParameters(data.blocks))
        changeCalcFromSalePrice(data.calcFromSalePrice)
        changePriceType(data.priceType)
        changeCheckRRC(data.check_rrc)
        store.dispatch(changeRRCSuppliers(data.rrcSuppliers))
        addLog(`Загружена формула "${data.current.title}"`)
    }

    if( action.type == RESET_FORMULA ){
        let deletedId = action.payload as number

        if( deletedId == 0 || store.getState().formulas.current.id == deletedId ){
            store.dispatch(changeFormula(null))
            store.dispatch(changeParameters([]))
            store.dispatch(changeRRCSuppliers([]))
        }
    }

    if( action.type == MAKE_NEW_EMPTY_FORMULA ){
        store.dispatch(changeFormula({comment: "", date: "", id: 0, manager: "", title: ""}))
        store.dispatch(changeParameters([{}]))
        store.dispatch(changeRRCSuppliers([]))
    }

    next(action);
}
