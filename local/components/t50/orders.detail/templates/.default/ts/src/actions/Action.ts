import { store } from "@Root/Stor";

export default abstract class Action
{
    static dispatch(type: string, data: any){
        store.dispatch({type, payload: data})
    }

    static updateLogs(){
        let order_id = store.getState().order.order_id
        T50PubSub.subscribe("load_history", async function (data){
            let html = await data
            $("#load_history").html(html)
        })
        T50PubSub.send("load_history", T50Ajax.postHtml("orders.detail", "load_history", {order_id}))
    }

    static showMessage<T>(answer: T50AjaxResult<T>, errorMessage: string){
        if( answer.result ){
            T50Notify.success("обновлено");
        } else {
            console.error(answer);
            if( (errorMessage ?? "").length )
                T50Notify.error(errorMessage);
        }
    }

    static getOrderId(){
        return Action.getState()?.order?.order_id
    }

    static getState(){
        return store.getState();
    }
}