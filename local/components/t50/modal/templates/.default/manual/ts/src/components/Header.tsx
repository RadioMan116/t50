import { h } from "preact";
import { ModalKey } from "../types_consts";

export default (props: {modalKey: ModalKey}) => {

    let map: {[code in ModalKey]: string} = {
        // products
        avail_supplier: "Ручное значение статуса наличия",
        avail_shop: "Ручное значение статуса наличия",
        purchase: "Ручное значение цены закупки",
        sale: "Ручное значение цены продажи",

        // baskets
        basket_sale: "Ручное значение цены продажи",
        basket_purchase: "Ручное значение цены закупки",
        install_sale: "Ручное значение цены продажи",
        install_purchase: "Ручное значение цены закупки",
    }


    if( !map[props.modalKey] )
        return

    return <h3 class="modal__title modal__title_align_center">{map[props.modalKey]}</h3>
}