import { createStore, Store, applyMiddleware } from "redux";
import RootReducer, { IRootReducer } from "./reducer";

const customMiddleWare = store => next => action => {
    next(action);
    console.log("action", action);
}

export const store: Store<IRootReducer>  = createStore(RootReducer,
    // applyMiddleware(customMiddleWare)
);