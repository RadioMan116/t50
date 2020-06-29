import { createStore, Store, applyMiddleware } from "redux";
import { RootReducer, IRootReducer } from "./reducers";

const customMiddleWare = store => next => action => {
    console.log("action", action);
    // console.time(action.type);
    // next(action);
    // console.timeEnd(action.type);
}
// export const store = createStore(RootReducer, applyMiddleware(customMiddleWare)) as Store<IRootReducer>;

export const store: Store<IRootReducer>  = createStore(RootReducer);
