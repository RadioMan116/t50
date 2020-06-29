import { combineReducers } from "redux";
import common, { ICommonData } from "./common";
import suppliers, { ISuppliers } from "./suppliers";
import parameters, { IParameters } from "./parameters";
import formulas, { IFormulas } from "./formulas";

export interface IRootReducer{
    common: ICommonData,
    suppliers: ISuppliers,
    parameters: IParameters
    formulas: IFormulas
}

export const RootReducer = combineReducers({common, suppliers, parameters, formulas});