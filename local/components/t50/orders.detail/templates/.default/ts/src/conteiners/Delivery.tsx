import { Component, h } from "preact";
import { IRootReducer } from "@Root/reducers";
import { connect } from "preact-redux";
import BaseComponent from "./BaseComponent";
import Select, { SelectItem } from "@Root/components/Select";
import { IDelivery, BasketDeliveryItem, DeliveryCond } from "@Root/reducers/delivery";
import Tools from "@Root/tools/Tools";
import Checkbox from "@Root/components/Checkbox";
import DeliveryAction from "@Root/actions/DeliveryAction";
import DateTimePicker from "@Root/components/DateTimePicker";
import { OrderId } from "@Root/types";
import BasketTools from "@Root/tools/BasketTools";
import CommonTools from "@Root/tools/CommonTools";
import DeliveryTools from "@Root/tools/DeliveryTools";

class Delivery extends BaseComponent<IMapStateToProps>
{
    componentWillMount(){
        T50PubSub.subscribe("change_flag_once_supplier__delivery", (checked) => {
            console.log("checked", checked);
        })
    }

    changeInput(item: BasketDeliveryItem, code: keyof BasketDeliveryItem, e: Event){
        let target = e.target as HTMLInputElement
        DeliveryAction.setValue(this.props.order_id, item.basket_id, code, target.value)
    }

    changeSelect(item: BasketDeliveryItem, code: keyof BasketDeliveryItem, select: SelectItem){
        if( code == "condition" ){
            let condition = this.props.conditions.find(item => item.val == select.val);
            if( (condition?.pickup || condition?.tk) && !confirm(`При установе условия "${condition.title}" будут сброшены цены. Продолжить?`) )
                return
        }
        DeliveryAction.setValue(this.props.order_id, item.basket_id, code, select.val)
    }

    getItems() {
        return this.props.items.map(item => {
            let basket = BasketTools.getById(item.basket_id)

            let trClass = "table__tr"
            if( basket?.claim )
                trClass += " table__tr_state_disabled"

            if( basket?.claim_replace )
                trClass += " table__tr_style_marked"

            let condition = this.props.conditions.find(cond => cond.val == item.condition)
            let pickup = (condition?.pickup == true)
            let tk = (condition?.tk == true)

            let timeRange = CommonTools.indexArrayToSelectItems(item.time_range);
            let time = DeliveryTools.getTimeVal(item.time, item.time_range);

            return <tr class={trClass}>
                <td class="table__td">
                    <div class="table__td-disabled">
                        <div class="order__field order__field_size-z_l">
                            <a class="link link_styel_classic" href={basket?.url}>{basket?.title}</a>
                        </div>
                    </div>
                </td>
                <td class="table__td">
                    <div class="table__td-disabled">
                        <div class="order__field_size-z_xs">{CommonTools.getSelectItem(basket?.supplier_id, this.props.suppliers)}</div>
                    </div>
                </td>
                <td class="table__td">
                    <div class="table__td-disabled">
                        <div class="order__field_size-z_l">
                            <Select key={Select.key()} items={this.props.conditions} onSelect={this.changeSelect.bind(this, item, "condition")} value={condition?.val} class="js-select form__select" />
                        </div>
                    </div>
                </td>
                <td class="table__td">
                    <div class="table__td-disabled">
                        <div class="form__field form__field_size-m_s">
                            <DateTimePicker type="date" value={item.date} onChange={this.changeInput.bind(this, item, "date")}/>
                        </div>
                    </div>
                </td>
                <td class="table__td">
                    <div class="table__td-disabled">
                        <div class="form__field form__field_size-s_l">
                            <Select key={Select.key()} items={timeRange} onSelect={this.changeSelect.bind(this, item, "time")} value={time} class="js-select form__select" default={{val:-1, title:"-", disabled: false}}/>
                        </div>
                    </div>
                </td>
                <td class="table__td">
                    <div class="table__td-disabled">
                        <div class="line-group">
                            <div class="line-group__item">
                                <div class="order__field order__field_size-z_xs">
                                    <input value={Tools.fnum(item.cost)} class="form__input" onChange={this.changeInput.bind(this, item, "cost")}/>
                                </div>
                            </div>
                            {this.getWhoPaySelect(item, "cost_us", item.cost_us)}
                        </div>
                    </div>
                </td>
                <td class="table__td">
                    { pickup || tk ? null :
                    <div class="table__td-disabled">
                        <div class="line-group">
                            <div class="line-group__item">
                                <div class="order__field order__field_size-z_xs">
                                    <input value={item.mkad_km} class="form__input" onChange={this.changeInput.bind(this, item, "mkad_km")}/>
                                </div>
                            </div>
                            <div class="line-group__item">
                                <div class="order__field order__field_size-z_xs">
                                    <input value={item.mkad_price} class="form__input" onChange={this.changeInput.bind(this, item, "mkad_price")}/>
                                </div>
                            </div>
                            <div class="line-group__item">
                                <div class="order__field order__field_size-z_xs">
                                    <input value={Tools.fnum(item.mkad_sum)} readOnly class="form__input" />
                                </div>
                            </div>
                            {this.getWhoPaySelect(item, "mkad_us", item.mkad_us)}
                        </div>
                    </div>
                    }
                </td>
                <td class="table__td">
                    { pickup || tk ? null :
                    <div class="table__td-disabled">
                        <div class="line-group">
                            <div class="line-group__item">
                                <div class="order__field order__field_size-z_xs">
                                    <input value={Tools.fnum(item.vip)} class="form__input" onChange={this.changeInput.bind(this, item, "vip")}/>
                                </div>
                            </div>
                            {this.getWhoPaySelect(item, "vip_us", item.vip_us)}
                        </div>
                    </div>
                    }
                </td>
                <td class="table__td">
                    { pickup || tk ? null :
                    <div class="table__td-disabled">
                        <div class="line-group">
                            <div class="line-group__item">
                                <div class="order__field order__field_size-z_xs">
                                    <input value={Tools.fnum(item.lift)} class="form__input" onChange={this.changeInput.bind(this, item, "lift")}/>
                                </div>
                            </div>
                            {this.getWhoPaySelect(item, "lift_us", item.lift_us)}
                        </div>
                    </div>
                    }
                </td>
                <td class="table__td">
                    { tk ? null :
                    <div class="table__td-disabled">
                        <div class={"order__field order__field_size-z_m" + (basket?.claim_replace ? /* " order__top-connect" */ "" : "")}>
                            <input value={item.pickup_address} class="form__input" onChange={this.changeInput.bind(this, item, "pickup_address")}/>
                        </div>
                    </div>
                    }
                </td>

                <td class="table__td">
                    <Checkbox checked={basket?.claim}/>
                </td>

            </tr>
        })
    }

    getWhoPaySelect(item:BasketDeliveryItem, code: "cost_us" | "mkad_us" | "vip_us" | "lift_us", wePay: boolean) {
        const items: SelectItem[] = [
            {val: 1, title: "С нас"},
            {val: 0, title: "С клиента"},
        ]
        let val = ( wePay ? 1 : 0 )
        return <div class="line-group__item">
            <div class="order__field_size-z_m">
                <Select key={Select.key()} items={items} onSelect={this.changeSelect.bind(this, item, code)} value={val} class="js-select form__select" />
            </div>
        </div>
    }

    render() {
        return <div>
            <div class="table">
                <table class="table__main">
                    <thead class="table__thead">
                        <tr class="table__tr">
                            <th class="table__th">Наименование</th>
                            <th class="table__th">Поставщик</th>
                            <th class="table__th">Условия</th>
                            <th class="table__th">Дата</th>
                            <th class="table__th">Время</th>
                            <th class="table__th">Стоимость</th>
                            <th class="table__th">За МКАД [Км] [Руб/км]</th>
                            <th class="table__th">VIP доставка</th>
                            <th class="table__th">Подъем</th>
                            <th class="table__th">Адрес самовывоза</th>
                            <th class="table__th">Рекламация</th>
                        </tr>
                    </thead>
                    <tbody class="table__tbody">
                        {this.getItems()}
                    </tbody>
                </table>
            </div>
            <div class="panel__total">
                <div class="form__item">Итого с клиента: <span class="panel__important">{this.props.price_client}</span></div>
                <div class="form__item">Итого с нас: <span class="panel__important">{this.props.price_shop}</span></div>
            </div>
        </div>
    }
}




type IMapStateToProps = IDelivery & OrderId & {conditions: DeliveryCond[], suppliers: SelectItem[]}

const mapStateToProps = (state: IRootReducer): IMapStateToProps => {
    return {
        ...state.delivery,
        order_id: state.order.order_id,
        conditions: state.order.deliveryConditions,
        suppliers: state.order.suppliers
    }
}

export default connect(mapStateToProps)(Delivery)