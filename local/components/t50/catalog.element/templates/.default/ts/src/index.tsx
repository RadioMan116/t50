import {render, h} from "preact"
import Comments, { CommentProps } from "./Comments";
import Basket from "./Basket";
import Analog from "./Analog";
import Discontinued from "./Discontinued";

$(document).ready(() => {

    const commentsBlock = document.getElementById("comments_block");
    let props = $(commentsBlock).data() as CommentProps
    render(<Comments {...props}/>, commentsBlock)

    new Basket

    $(".js_change_city").change(function () {
        document.location.search = "city=" + $(this).val();
    });

    $('.js_change_brand_formula').on('select2:select', async function (e) {
        let brand_id = $(this).data("brand_id");
        let formula_id = $(this).val()
        let answer = await T50Ajax.postJson("catalog.element", "set_formula_for_brand", {brand_id, formula_id})
        if( answer.result ){
            T50Notify.success("Обновлено")
        } else {
            T50Notify.error("Ошибка")
        }
    });

    new Discontinued()
    new Analog();
});