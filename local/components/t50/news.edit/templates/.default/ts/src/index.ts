import Files from "./Files";
import Groups from "./Groups";

$(document).ready(() => {
    $(".js-delete_news").click((e) => {
        if( !confirm("Удалить новость?") )
            e.preventDefault()
    });
    new Files();
    new Groups();
});