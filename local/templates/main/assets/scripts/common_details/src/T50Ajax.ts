type PostDataType = string | {} | FormData
export interface T50AnswerType<T> {
    result: "success" | "fail",
    message?: string,
    errors?: {},
    data?: T
};

export interface T50AjaxResult<T> {
    result: boolean,
    message?: string,
    errors?: {},
    data?: T
};

export default class T50Ajax {
    static post<T>(component: string, action: string, data: PostDataType, getData?: {} | string) {
        return T50Ajax.__exec<T>(component, action, data, getData, { dataType: "json" });
    }

    static postHtml(component: string, action: string, data: PostDataType, getData?: {} | string): Promise<string> {
        return T50Ajax.__exec(component, action, data, getData, { dataType: "html" }) as Promise<string>;
    }

    static postFormDataHtml(component: string, action: string, data: PostDataType, getData?: {} | string): Promise<string> {
        let config = { cache: false, dataType: 'html', contentType: false, processData: false }
        return T50Ajax.__exec(component, action, data, getData, config) as Promise<string>;
    }

    static postJson<T>(component: string, action: string, data: PostDataType, getData?: {} | string): Promise<T50AjaxResult<T>> {
        return T50Ajax.__exec(component, action, data, getData, { dataType: "json" }) as Promise<T50AjaxResult<T>>;
    }

    static postFormData<T>(component: string, action: string, data: PostDataType, getData?: {} | string): Promise<T50AjaxResult<T>> {
        let config = { cache: false, dataType: 'json', contentType: false, processData: false }
        return T50Ajax.__exec(component, action, data, getData, config) as Promise<T50AjaxResult<T>>;
    }

    private static __exec<T>(component: string, action: string, data?: PostDataType, getData?: {} | string, config = {}): Promise<T50AjaxResult<T> | string> {
        const sessid = $("meta[name='sessid']").attr("content")

        let url = `/ajax/${component}/${action}/`;
        if (getData != null)
            url = T50Ajax.__prepareGetParams(url, getData)

        if( data instanceof FormData )
            data.append("sessid", sessid)

        const success = resolve => (answer: T50AnswerType<T> | string) => {
            if (typeof answer == "string") {
                resolve(answer)
            } else {
                resolve({
                    result: (answer.result === "success"),
                    message: answer.message,
                    errors: answer.errors,
                    data: answer.data
                })
            }
        }

        const error = resolve => (errors: {}) => {
            resolve({ result: false, message: "system error", errors })
        }

        return new Promise((resolve) => {
            let ajaxConfig = {
                url: url,
                type: "post",
                dataType: "html",
                data: data,
                headers: { "X-CSRF-TOKEN": sessid },
                success: success(resolve),
                error: error(resolve),
            }
            Object.assign(ajaxConfig, config);
            $.ajax(ajaxConfig);
        })
    }

    private static __prepareGetParams(url: string, data: {} | string): string {
        let char = (url.indexOf("?") == -1 ? "?" : "&");
        url += char;

        if (typeof data == "string")
            return url + data

        let params = [];
        for (let code in data) {
            params.push(`${code}=${data[code]}`);
        }
        return url + params.join("&")
    }
}