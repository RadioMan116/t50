type SetAnalogData = {
    product_id: number,
    analog_id: number,
    comment: string,
}

export default class Analog
{
    private root: JQuery;

    constructor(){
        this.root = $("#product_analog");
        if( this.root.length == 0 )
            return;

        this.load(this.root.data());
        T50PubSub.subscribe("set_analog_submit", this.setAnalog.bind(this), this);
    }

    async setAnalog(data: SetAnalogData){
        if( !data.comment || data.comment.length < 5 || data.product_id <= 0 || data.analog_id <= 0 )
            return;

        let answer = await T50Ajax.postJson("catalog.element", "set_analog", data);
        if( answer.result ){
            T50Notify.success("Обновлено");
            ($ as any).fancybox.close();
            this.load(this.root.data());
        } else {
            T50Notify.error("Ошибка");
        }
    }

    async load(postData: {}){
        if( postData["analog_id"] <= 0 )
            return;

        let data = await T50Ajax.postHtml("catalog.element", "analog", postData)
        this.root.html(data);
    }
}