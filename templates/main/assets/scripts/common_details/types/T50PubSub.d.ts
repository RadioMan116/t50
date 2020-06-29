export declare type PubSubHandler = (arg: any) => any;
export declare type Subscriber = {
    handler: PubSubHandler;
    context?: any;
};
export default class T50PubSub {
    private static subscribers;
    static subscribe<T>(event_type: string, handler: PubSubHandler, context?: any, once?: boolean): void;
    static subscribeMany<T>(event_type: string, handler: PubSubHandler, context?: any): void;
    static send(event_type: string, data: any): void;
}
