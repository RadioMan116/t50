declare type PostDataType = string | {} | FormData;
export interface T50AnswerType<T> {
    result: "success" | "fail";
    message?: string;
    errors?: {};
    data?: T;
}
export interface T50AjaxResult<T> {
    result: boolean;
    message?: string;
    errors?: {};
    data?: T;
}
export default class T50Ajax {
    static post<T>(component: string, action: string, data: PostDataType, getData?: {} | string): Promise<string | T50AjaxResult<T>>;
    static postHtml(component: string, action: string, data: PostDataType, getData?: {} | string): Promise<string>;
    static postFormDataHtml(component: string, action: string, data: PostDataType, getData?: {} | string): Promise<string>;
    static postJson<T>(component: string, action: string, data: PostDataType, getData?: {} | string): Promise<T50AjaxResult<T>>;
    static postFormData<T>(component: string, action: string, data: PostDataType, getData?: {} | string): Promise<T50AjaxResult<T>>;
    private static __exec;
    private static __prepareGetParams;
}
export {};
