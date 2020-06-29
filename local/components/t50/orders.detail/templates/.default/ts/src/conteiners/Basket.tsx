import { Component, h } from "preact"
import { connect } from "preact-redux"
import { IRootReducer } from "@Reducers/index";
import SvgIcon from "@Components/SvgIcon"
import BaseComponent from "./BaseComponent";
import Checkbox from "@Components/Checkbox";
import QuantityInput from "@Components/QuantityInput";
import Tools from "@Root/tools/Tools";
import Select, { SelectItem } from "@Root/components/Select";
import Hand from "@Root/components/Hand";
// import { tooltip, modal } from "@Root/services/JQueryUI";
import { BasketItem, PayType } from "@Root/reducers/basket";
import BasketAction from "@Root/actions/BasketAction";
import { ModalKey, ModalData } from "@Root/types";
import CommonAction from "@Root/actions/CommonAction";
import CommonTools from "@Root/tools/CommonTools";


class Basket extends BaseComponent<IMapStateToProps>
{
    private tabls = false

    constructor(props: IMapStateToProps) {
        super(props);
        this.tabls = !!$(".tabs").length

        // open products panel
        T50PubSub.subscribe("load_products", async (html) => {
            $("#ajax_basket_load_products").html(await html)
            $("#ajax_basket_load_products").slideDown(350)
        })

        // select product and close panel
        T50PubSub.subscribe("select_product_for_basket", (data: {exchange_basket_id: number, product_price_id: number}) => {
            BasketAction.create(this.props.order_id, data.product_price_id, data.exchange_basket_id)
            $("#ajax_basket_load_products").slideUp(350)
        })

        // manual modal onSubmit
        T50PubSub.subscribeMany("manual_modal_submit", async (data: ModalData) => {
            if( !data.modal_key.includes("basket") )
                return;
            let success = await BasketAction.setManual(this.props.order_id, data.bind_id, data.value, data.comment, data.modal_key);
            if( success )
                ($ as any).fancybox.close();
        })
    }

    componentDidUpdate(){
        super.componentDidUpdate();
        this.jqueryUpdate = false;
    }

    loadProducts(exchangeBasketId = 0){
        let data = {}
        if( exchangeBasketId > 0 )
            data["exchange_basket_id"] = exchangeBasketId

        // open products panel
        T50PubSub.send("load_products", T50Ajax.postHtml("catalog.default", "load_products", data))
    }

    changeQuantity(item: BasketItem, count: number){
        BasketAction.setValue(this.props.order_id, item.id, "quantity", count)
    }

    changePayType(item: BasketItem, select: SelectItem){
        BasketAction.setValue(this.props.order_id, item.id, "pay_type", select.val as number)
    }

    async setClaim(item: BasketItem, checked: boolean){
        if( checked == false && !confirm("Снять с рекламации?\nЗаменяющий товар будет удален со всеми данными.") )
            return false;

        let success = await BasketAction.setValue(this.props.order_id, item.id, "claim", (checked ? 1 : 0), true)
        return success;
    }

    setOrderFlags(code: "agreed_client" | "agreed_supplier", value: boolean){
        CommonAction.setValue(code, (value ? 1 : 0))
    }

    delete(item: BasketItem){
        if( !confirm(`Удалить товар "${item.title}"?`) )
            return

        BasketAction.delete(this.props.order_id, item.id)
    }

    getProductName(item: BasketItem){
        if( item.url == null )
            return item.title;

        return <a class="link link_styel_classic" target="_blank" href={item.url}>{item.title}</a>
    }

    getBasketItems() {
        return this.props.items.map(item => {
            let trClass = "table__tr"
            if( item.claim )
                trClass += " table__tr_state_disabled"

            if( item.claim_replace )
                trClass += " table__tr_style_marked"

            return <tr class={trClass}>
                <td class="table__td">
                    <div class="table__td-disabled">
                        {this.getProductName(item)}
                    </div>
                </td>
                <td class="table__td">
                    <div class="table__td-disabled">
                        <QuantityInput value={item.quantity} min={1} onChange={this.changeQuantity.bind(this, item)} />
                    </div>
                </td>
                <td class="table__td">
                    <div class="table__td-disabled">
                        <div class="form__field form__field_size-m_s">{CommonTools.getSelectItem(item.supplier_id, this.props.suppliers)}</div>
                    </div>
                </td>
                <td class="table__td">
                    <Hand isManual={item.is_manual_sale} value={item.sale} comment={item.comments?.sale} manualKey="basket_sale" id={item.id} order_id={this.props.order_id}/>
                </td>
                <td class="table__td">
                    <Hand isManual={item.is_manual_purchase} value={item.purchase} comment={item.comments?.purchase} manualKey="basket_purchase"  id={item.id}  order_id={this.props.order_id}/>
                </td>
                <td class="table__td">
                    <div class="table__td-disabled">{Tools.fnum(item.commission)}</div>
                </td>
                <td class="table__td">
                    <div class="table__td-disabled">
                        <div class={"form__field form__field_size-xl_m" + (item.claim_replace ? " order__top-connect" : "")}>
                            <Select key={Select.key()} items={this.props.pay_types} onSelect={this.changePayType.bind(this, item)} value={item.pay_type} class="js-select form__select"/>
                        </div>
                    </div>
                </td>

                {item.claim?
                <td class="table__td table__not-disabled">
                    <div class="form__field form__field_size-xl_m">
                        <div class="form__item">
                            <Checkbox checked={true} onClick={this.setClaim.bind(this, item)} awaitConfirm={true}/>
                        </div>
                        <div class="form__item pd-group">
                            <button class="button" type="button" onClick={this.loadProducts.bind(this, item.id)}>Добавить замену</button>
                        </div>
                    </div>
                </td>
                :
                <td class="table__td">
                    <div class="form__field form__field_size-xl_xs">
                        <Checkbox onClick={this.setClaim.bind(this, item)} />
                    </div>
                </td>
                }

                <td class="table__td table__td_align_right">
                    <div class="table__td-disabled">

                        <div class="link link_icon_big" onClick={this.delete.bind(this, item)}>
                            <SvgIcon name="icon_delete" class="link__icon"/>&nbsp;
                    </div>
                    </div>
                </td>
            </tr>
        })
    }

    render() {
        return <div>
            <div class="form__section">
                <span class="panel__label panel__label_size_big">Согласование:</span>
                <div class="form__item">
                    <Checkbox checked={this.props.agreed_client} text="Согласовано клиент" onClick={this.setOrderFlags.bind(this, "agreed_client")}/>
                </div>
                <div class="form__item">
                    <Checkbox checked={this.props.agreed_supplier} text="Согласовано поставщик" onClick={this.setOrderFlags.bind(this, "agreed_supplier")}/>
                </div>
            </div>

            <span class={"panel__title " + (this.tabls ? "panel_type_subtitle" : "panel__title_type_subtitle")}>
                Состав заказа
            </span>

            <div class="table form">
                <table class="table__main">
                    <thead class="table__thead">
                        <tr class="table__tr">
                            <th class="table__th">Наименование</th>
                            <th class="table__th">Количество</th>
                            <th class="table__th">Поставщик</th>
                            <th class="table__th">Цена продажи</th>
                            <th class="table__th">Цена закупки</th>
                            <th class="table__th">Комиссия</th>
                            <th class="table__th">Способ оплаты</th>
                            <th class="table__th">Рекламация</th>
                            <th class="table__th">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody class="table__tbody">
                        {this.getBasketItems()}
                    </tbody>
                    <tfoot class="table__tfoot">
                        <tr class="table__tr table__tr_style_ninja">
                            <th class="table__td">
                                <div class="table__marked">Итого:</div>
                            </th>
                            <th class="table__td">&nbsp;</th>
                            <th class="table__td">&nbsp;</th>
                            <th class="table__td">
                                <div class="marked status_failed">{Tools.fnum(this.props.basket_sale_sum)}</div>
                            </th>
                            <th class="table__td">
                                <div class="marked status_failed">{Tools.fnum(this.props.basket_purchase_sum)}</div>
                            </th>
                            <th class="table__td">
                                <div class="marked status_failed">{Tools.fnum(this.props.basket_commission_sum)}</div>
                            </th>
                            <th class="table__td"> &nbsp;</th>
                            <th class="table__td"> &nbsp;</th>
                            <th class="table__td">&nbsp;</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="panel__foot panel__foot_type_full pd-group">
                <div class="panel__main">
                    <button class="button" type="button" onClick={this.loadProducts.bind(this)}>Добавить к заказу</button>
                </div>
                <div class="panel__addition">
                    <a class="js-modal link link_icon_big link_type_ninja link_icon_marked" href="#email_editor" data-email_type="*" data-order_id={this.props.order_id}>
                        <SvgIcon name="icon_e-mail" class="link__icon" />
                        Отправить на почту поставщику</a>
                </div>
            </div>
        </div>
    }
}

interface IMapStateToProps {
    agreed_client: boolean,
    agreed_supplier: boolean,
    items: BasketItem[],
    pay_types: PayType[],
    basket_sale_sum: number,
    basket_purchase_sum: number,
    basket_commission_sum: number,
    order_id: number,
    suppliers: SelectItem[],
}

const mapStateToProps = (state: IRootReducer): IMapStateToProps => ({
    agreed_client: state.order.agreed_client,
    agreed_supplier: state.order.agreed_supplier,
    items: state.basket.items,
    pay_types: state.basket.pay_types,
    basket_sale_sum: state.basket.sale_sum,
    basket_purchase_sum: state.basket.purchase_sum,
    basket_commission_sum: state.basket.commission_sum,
    order_id: state.order.order_id,
    suppliers: state.order.suppliers,
});

export default connect(mapStateToProps)(Basket)