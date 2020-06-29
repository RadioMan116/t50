export default class Unread
{
    constructor(){
        $(".js-checkbox-unread").click(this.switch.bind(this))
    }

    async switch(e: Event){
        let checkbox = e.currentTarget as HTMLInputElement
        let id = checkbox.value;
        let answer = await T50Ajax.postJson("news.default", "set_as_read", {id});
        if( answer.result ){
            $(checkbox).closest(".check-elem").remove()
            document.location.reload()
        } else {
            T50Notify.error("ошибка")
        }
    }
}