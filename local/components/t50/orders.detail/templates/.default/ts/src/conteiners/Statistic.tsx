import { Component, h } from "preact";
import { IRootReducer } from "@Root/reducers";
import { connect } from "preact-redux";
import BaseComponent from "./BaseComponent";
import Select, { SelectItem } from "@Root/components/Select";
import CommonTools from "@Root/tools/CommonTools";
import Tools from "@Root/tools/Tools";
import CommonAction from "@Root/actions/CommonAction";
import SvgIcon from "@Root/components/SvgIcon";

class Statistic extends BaseComponent<IMapStateToProps> {

    changeStatus(status: SelectItem) {
        CommonAction.setValue("status", parseInt(status.val as string))
    }

    componentDidUpdate() {
        super.componentDidUpdate();
        this.jqueryUpdate = false;
    }

    render() {
        let props = this.props

        return <div class="table table_style_simple table_width_auto">
            <table class="table__main">
                <div class="table__tr">
                    <td class="table__td">
                        <label class="form__line form__field form__field_size-xl_xs">
                            <div class="form__label">Магазин</div>
                            <input value={props.shop} readOnly class="form__input" />
                        </label>
                    </td>
                    <td class="table__td">
                        <label class="form__line form__field form__field_size-l_xs">
                            <div class="form__label">№&nbsp;заказа&nbsp;магазина</div>
                            <input value={(props.remote_order_id > 0 ? props.remote_order_id : "")} readOnly class="form__input" />
                        </label>
                    </td>
                    <td class="table__td">
                        <label class="form__line form__field form__field_size-xl_xs">
                            <div class="form__label">Менеджер</div>
                            <input value={CommonTools.getManager(this.props.manager_id)} readOnly class="form__input" />
                        </label>
                    </td>
                    <td class="table__td">
                        <label class="form__line form__field form__field_size-xl_xs">
                            <div class="form__label">Источник</div>
                            <input value={CommonTools.getSelectItem(props.source_id, props.sources)} readOnly class="form__input" />
                        </label>
                    </td>
                    <td class="table__td">
                        <label class="form__line form__field form__field_size-xl_xs status_in-progress marked">
                            <div class="form__label">Статус</div>
                            <Select class="js-select form__select" items={props.statuses} value={props.status_id} onSelect={this.changeStatus.bind(this)} />
                        </label>
                    </td>
                    <td class="table__td">
                        <label class="form__line form__field form__field_size-xl_xs">
                            <div class="form__label">Дата обработки</div>
                            <input value={props.dateCreate} readOnly class="form__input" />
                        </label></td>
                    <td class="table__td">
                        <label class="form__line form__field form__field_size-xl_xs">
                            <div class="form__label">Дата отгрузки</div>
                            <input value={props.delivery_date} readOnly class="form__input" />
                        </label>
                    </td>
                    <td class="table__td">
                        <label class="form__line form__field form__field_size-m_s">
                            <div class="form__label">Комиссия&nbsp;(руб.)</div>
                            <input value={Tools.fnum(props.comission)} readOnly class="form__input" />
                        </label>
                    </td>
                </div>
            </table>
        </div>

    }
}


interface IMapStateToProps {
    shop: string,
    remote_order_id: number,
    manager_id: number,
    source_id: number,
    status_id: number,
    statuses: SelectItem[],
    sources: SelectItem[],
    dateCreate: string,
    delivery_date: string,
    comission: number,
}

const mapStateToProps = (state: IRootReducer): IMapStateToProps => ({
    shop: state.order.shop,
    remote_order_id: state.order.remote_order_id,
    manager_id: state.order.manager,
    source_id: state.order.source,
    status_id: state.order.status,
    sources: state.order.sources,
    statuses: state.order.statuses,
    dateCreate: state.order.dateCreate,
    delivery_date: state.profit.delivery_date,
    comission: state.profit.full_commission,
});

export default connect(mapStateToProps)(Statistic)