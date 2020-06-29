import { Component, h } from "preact";
import { IRootReducer } from "@Root/reducers";
import { connect } from "preact-redux";
import SvgIcon from "@Components/SvgIcon"
import BaseComponent from "./BaseComponent";
import Checkbox from "@Root/components/Checkbox";
import { IInstallation, InstallationItem } from "@Root/reducers/installation";
import Select, { SelectItem } from "@Root/components/Select";
import DateTimePicker from "@Root/components/DateTimePicker";
import InstallationAction from "@Root/actions/InstallationAction";
import { OrderId, InstallationPriceData } from "@Root/types";
import BasketTools from "@Root/tools/BasketTools";
import InstallationTools from "@Root/tools/InstallationTools";
import CommonTools from "@Root/tools/CommonTools";
import Tools from "@Root/tools/Tools";

class Installation extends BaseComponent<IMapStateToProps>
{

    componentWillMount() {
        T50PubSub.subscribe("select_installation_service", async (data: InstallationPriceData) => {
            let success = false;
            if( data.installation_id > 0 ){
                let basketId = InstallationTools.getBasketId(data.installation_id, this.props.items)
                success = await InstallationAction.setService(basketId, data.installation_id, data);
            } else {
                success = await InstallationAction.create(data);
            }

            if( success )
                ($ as any).fancybox.close();
        })
    }

    componentDidUpdate(){
        super.componentDidUpdate();
        this.jqueryUpdate = false;
    }

    changeInput(item: InstallationItem, name: keyof InstallationItem, e: Event) {
        let target = e.target as HTMLInputElement
        InstallationAction.setValue(item.basket_id, item.id, name, target.value)
    }

    changeSelect(item: InstallationItem, name: keyof InstallationItem, select: SelectItem) {
        InstallationAction.setValue(item.basket_id, item.id, name, select.val)
    }

    delete(item: InstallationItem){
        if( confirm("Удалить заявку?") )
            InstallationAction.delete(item.id)
    }

    sentToRemcity(){
        let conf = confirm("Отправить заявку в remcity.net?\nПозиции с поставщиком РемСити будут заблокированы.");
        if( conf )
            InstallationAction.sentToRemcity();
    }

    getItems() {
        return this.props.items.map(item => {
            let basket = BasketTools.getById(item.basket_id)

            let trClass = "table__tr"
            if (basket?.claim || !item.can_change )
                trClass += " table__tr_state_disabled"

            if (basket?.claim_replace)
                trClass += " table__tr_style_marked"

            let title = ( (item.product_name != null && item.product_name.length > 0) ? item.product_name : basket?.title );

            return <tr class={trClass}>
                <td class="table__td">
                    <div class="table__td-disabled">
                        <div class="order__field order__field_size-z_l">
                            <a class="link link_styel_classic" href={basket?.url}>{title}</a>
                        </div>
                    </div>
                </td>
                <td class="table__td">
                    <div class="table__td-disabled">
                        <div class="order__field order__field_size-z_l">
                        <Select key={Select.key()} items={[{val: "installation", title: "Установка"},{val: "repair", title: "Ремонт"}]} onSelect={this.changeSelect.bind(this, item, "type")} value={item.type} class="js-select form__select" />
                        </div>
                    </div>
                </td>
                <td class="table__td">
                    <div class="table__td-disabled">
                        <div class="order__field_size-z_l">
                            <a class="link link_styel_classic js-modal" data-provider={item.provider} data-service_id={item.service_id} data-installation_id={item.id} href="#installation_prices">
                                {CommonTools.getSelectItem(item.provider, this.props.providers) ?? "-"}
                            </a>
                        </div>
                    </div>
                </td>
                <td class="table__td">
                    <div class="table__td-disabled">
                        <div class="form__field form__field_size-m_s">
                            <DateTimePicker type="date" onChange={this.changeInput.bind(this, item, "date")} value={item.date} />
                        </div>
                    </div>
                </td>
                <td class="table__td">
                    <div class="table__td-disabled">
                        <div class="order__field order__field_size-z_s">
                            <input class="form__input"  value={item.sale} onChange={this.changeInput.bind(this, item, "sale")}/>
                        </div>
                    </div>
                </td>
                <td class="table__td">
                    <div class="table__td-disabled">
                        <div class="order__field order__field_size-z_s">
                            <input class="form__input"  value={item.purchase} onChange={this.changeInput.bind(this, item, "purchase")}/>
                        </div>
                    </div>
                </td>
                <td class="table__td">
                    <div class="table__td-disabled">
                        <div class="order__field_size-z_s">
                            {this.getWhoPaySelect(item, "costs_us", item.costs_us)}
                        </div>
                    </div>
                </td>
                <td class="table__td">
                    <div class="table__td-disabled">
                        <div class="order__field order__field_size-z_xs">
                            <input value={item.visit_master_price} onChange={this.changeInput.bind(this, item, "visit_master_price")} class="form__input" />
                        </div>
                    </div>
                </td>
                <td class="table__td">
                    <div class="table__td-disabled">
                        <div class="order__field order__field_size-z_xs">
                            <input value={item.visit_master_km} onChange={this.changeInput.bind(this, item, "visit_master_km")} class="form__input" />
                        </div>
                    </div>
                </td>
                <td class="table__td">
                    <div class="table__td-disabled">
                        <div class="order__field order__field_size-z_xs">
                            <input value={item.visit_master_km_price} onChange={this.changeInput.bind(this, item, "visit_master_km_price")} class="form__input" />
                        </div>
                    </div>
                </td>
                <td class="table__td">
                    <div class="table__td-disabled">
                        <div class="line-group">
                            <div class="line-group__item">
                                <div class="order__field order__field_size-z_xs">
                                    <input value={item.visit_master_sum} readOnly class="form__input" />
                                </div>
                            </div>
                            {this.getWhoPaySelect(item, "visit_master_us", item.visit_master_us)}
                        </div>
                    </div>
                </td>
                <td class="table__td">
                    <div class="table__td-disabled">
                        <div class="order__field order__field_size-z_xs">{item.commission}</div>
                    </div>
                </td>
                <td class="table__td">
                    <div class="table__td-disabled">
                        <div class={"order__field order__field_size-z_l" + (basket?.claim_replace ? /* " order__top-connect" */ "" : "")}>
                            <input value={item.comment} onChange={this.changeInput.bind(this, item, "comment")} class="form__input" />
                        </div>
                    </div>
                </td>
                <td class="table__td">
                    <Checkbox checked={basket?.claim} />
                </td>
                <td class="table__td table__td_align_right">
                    <div class="table__td-disabled">

                        <div class="link link_icon_big" onClick={this.delete.bind(this, item)}>
                            <SvgIcon name="icon_delete" class="link__icon" />&nbsp;</div>
                    </div>
                </td>
            </tr>
        })
    }

    getWhoPaySelect(item: InstallationItem, code: "costs_us" | "visit_master_us", wePay: boolean) {
        const items: SelectItem[] = [
            { val: 1, title: "С нас" },
            { val: 0, title: "С клиента" },
        ]
        let val = (wePay ? 1 : 0)
        return <div class="line-group__item">
            <div class="order__field_size-z_s">
                <Select items={items} onSelect={this.changeSelect.bind(this, item, code)} value={val} class="js-select form__select" />
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
                            <th class="table__th">Тип</th>
                            <th class="table__th">Поставщик</th>
                            <th class="table__th">Дата</th>
                            <th class="table__th">Цена продажи</th>
                            <th class="table__th">Цена закупки</th>
                            <th class="table__th">Условия</th>
                            <th class="table__th">Выезд мастера</th>
                            <th class="table__th">Км.</th>
                            <th class="table__th">Руб./км.</th>
                            <th class="table__th">Сумма</th>
                            <th class="table__th">Комиссия</th>
                            <th class="table__th">Комментарий</th>
                            <th class="table__th">Рекламация</th>
                            <th class="table__th">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody class="table__tbody">
                        {this.getItems()}
                    </tbody>
                </table>
            </div>
            <div class="panel__total panel__total_space_bottom">
                <div class="form__item">Итого с клиента: <span class="panel__important">{this.props.price_client}</span></div>
                <div class="form__item">Итого с нас: <span class="panel__important">{this.props.price_shop}</span></div>
                <div class="form__item pd-group">
                    <button class="button js-modal" data-custom_input="Y" href="#installation_prices" type="button" >Добавить товар</button>
                </div>

                { this.props.remcityOrderNum > 0  ?
                    <div class="form__item">Номер РемСити: <span class="panel__important">{this.props.remcityOrderNum}</span></div>
                    :
                    <div class="form__item pd-group">
                        <button class="button button_style_dark" type="button" onClick={this.sentToRemcity.bind(this)} disabled={Tools.isEmptyList(this.props.items)}>
                            Отправить в РемСити
                        </button>
                    </div>
                }
            </div>
        </div>
    }
}



type IMapStateToProps = IInstallation & OrderId & {providers: SelectItem[]}

const mapStateToProps = (state: IRootReducer): IMapStateToProps => {
    return {
        ...state.installation,
        order_id: state.order.order_id,
        providers:  state.order.installationProviders
    }
}

export default connect(mapStateToProps)(Installation)