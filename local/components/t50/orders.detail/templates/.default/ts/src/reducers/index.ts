import { combineReducers } from "redux";
import order, { IOrderData } from "./order";
import accounts, { IAccounts } from "./accounts";
import basket, { IBasket } from "./basket";
import client, { IClient } from "./client";
import comments, { IComments } from "./comments";
import delivery, { IDelivery } from "./delivery";
import docs, { IDocs } from "./docs";
import fine, { IFine } from "./fine";
import installation, { IInstallation } from "./installation";
import profit, { IProfit } from "./profit";
import reclamation, { IReclamation } from "./reclamation";
import once_supplier, { IOnceSupplier } from "./once_supplier";
import deduction, { IDeduction } from "./deduction";

export interface IRootReducer{
    order: IOrderData,
    accounts: IAccounts,
    basket: IBasket,
    client: IClient,
    comments: IComments,
    delivery: IDelivery,
    docs: IDocs,
    fine: IFine,
    installation: IInstallation,
    profit: IProfit,
    reclamation: IReclamation,
    once_supplier: IOnceSupplier,
    deduction: IDeduction,
}

export const RootReducer = combineReducers({
    order,
    accounts,
    basket,
    client,
    comments,
    delivery,
    docs,
    fine,
    installation,
    profit,
    reclamation,
    once_supplier,
    deduction
});