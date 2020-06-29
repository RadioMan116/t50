import { createStore, Store } from "redux";
import RootReducer, { IRootReducer } from "./reducer";

export const store: Store<IRootReducer>  = createStore(RootReducer);