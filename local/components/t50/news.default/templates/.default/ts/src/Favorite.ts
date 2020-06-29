type Data = {
    id: number,
    is_favorite: boolean
}
const FAVORITE_CLASS = "entry-snippet__favorites_state_active"
export default class Favorite
{


    constructor(){
        $(".js_favorite").click((e) => {
            e.preventDefault();
            this.switch($(e.currentTarget))
        })
    }

    async switch($item: JQuery){
        let data:Data = $item.data() as Data;
        let value = !data.is_favorite
        let answer = await T50Ajax.postJson("news.default", "switch_favorite", {id: data.id, value});
        if( answer.result ){
            if( value ){
                $item.addClass(FAVORITE_CLASS);
            } else {
                $item.removeClass(FAVORITE_CLASS);
            }
        } else {
            T50Notify.error("ошибка")
        }
    }
}