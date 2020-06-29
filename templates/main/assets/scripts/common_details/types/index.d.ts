declare type PostDataType = string | {} | FormData;
declare interface T50AnswerType<T> {
    result: "success" | "fail";
    message?: string;
    errors?: {};
    data?: T;
}
declare interface T50AjaxResult<T> {
    result: boolean;
    message?: string;
    errors?: {};
    data?: T;
}
declare class T50Ajax {
    static post<T>(component: string, action: string, data: PostDataType, getData?: {} | string): Promise<string | T50AjaxResult<T>>;
    static postHtml(component: string, action: string, data: PostDataType, getData?: {} | string): Promise<string>;
    static postFormDataHtml(component: string, action: string, data: PostDataType, getData?: {} | string): Promise<string>;
    static postJson<T>(component: string, action: string, data: PostDataType, getData?: {} | string): Promise<T50AjaxResult<T>>;
    static postFormData<T>(component: string, action: string, data: PostDataType, getData?: {} | string): Promise<T50AjaxResult<T>>;
    private static __exec;
    private static __prepareGetParams;
}

declare class T50Notify {
    private static INSTANCE;
    private block;
    private blockText;
    private queue;
    private timer;
    private running;
    constructor();
    private static getInstance;
    static success(msg?: string): void;
    static error(msg?: string): void;
    private __exec;
    private __show;
    private __close;
}
declare type PubSubHandler = (arg: any) => any;
declare type Subscriber = {
    handler: PubSubHandler;
    context?: any;
};
declare class T50PubSub {
    private static subscribers;
    static subscribe<T>(event_type: string, handler: PubSubHandler, context?: any, once?: boolean): void;
    static subscribeMany<T>(event_type: string, handler: PubSubHandler, context?: any): void;
    static send(event_type: string, data: any): void;
}
