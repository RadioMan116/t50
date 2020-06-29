import { createStore, Store, applyMiddleware, compose } from "redux";
import { RootReducer, IRootReducer } from "./reducers";
import * as middlwares from "./middlewares";

export const store: Store<IRootReducer>  = createStore(RootReducer, applyMiddleware(...Object.values(middlwares)));