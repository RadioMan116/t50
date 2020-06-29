import Base from "./Base";

type CommonData = {
    scroll_to_table: boolean,
    filter_error: boolean,
}
export default class CommonDataActions extends Base
{
    constructor(){
        super();
        let commonData = this.getCommonData();

        if( commonData.scroll_to_table ){
            $([document.documentElement, document.body]).scrollTop($(".js-scroll-to-this").offset().top);
        }

        if( commonData.filter_error ){
            T50Notify.error("Ошибка в фильтре");
        }
    }

    getCommonData() : CommonData {
        return window["component_data"] as CommonData;
    }
}