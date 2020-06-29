type ModalData = {
    ajax_id: string,
    bind_id: number,
    city: "MSK"| "SPB",
    comment: string,
    date_end?: string,
    id: number
    modal_key: "avail_supplier" | "avail_shop" | "purchase" | "sale"
    value: number
}
class HandComponent
{
    constructor(){
        T50PubSub.subscribe("manual_modal_submit", this.modalOnSubmit, this)
    }

    async modalOnSubmit(data: ModalData){
        let answer = await T50Ajax.postJson("catalog.element", "change_manual", data);
        if( !answer.result ){
            T50Notify.error("Ошибка")
            return
        }

        let block = $(`div[id=${data.ajax_id}]`);
        ($ as any).fancybox.close();
        let html = await T50Ajax.postHtml("hand_and_data", "refresh", data);

        block.html(html)
    }
}


$(document).ready(() => {
    new HandComponent
})