export type PubSubHandler = (arg:any) => any
export type Subscriber = {
    handler: PubSubHandler,
    context?: any,
}
export default class T50PubSub
{
    private static subscribers: {[event_type: string]: Subscriber[]} = {}

    static subscribe<T>(event_type: string, handler: PubSubHandler, context?: any, once = true){
        if( T50PubSub.subscribers[event_type] == null )
            T50PubSub.subscribers[event_type] = [];

        let subscriber:Subscriber = {handler, context}
        if( once ){
            T50PubSub.subscribers[event_type] = [subscriber]
        } else {
            T50PubSub.subscribers[event_type].push(subscriber)
        }
    }

    static subscribeMany<T>(event_type: string, handler: PubSubHandler, context?: any){
        T50PubSub.subscribe<T>(event_type, handler, context, false)
    }

    static send(event_type: string, data: any){
        if( T50PubSub.subscribers[event_type] == null )
            return

        T50PubSub.subscribers[event_type].forEach(subscriber => subscriber.handler.call(subscriber.context, data))
    }
}