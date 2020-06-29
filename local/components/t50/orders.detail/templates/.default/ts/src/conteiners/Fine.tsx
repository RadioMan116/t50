import { h, Component } from "preact";
import { IRootReducer } from "@Root/reducers";
import { connect } from "preact-redux";
import SvgIcon from "@Components/SvgIcon"
import BaseComponent from "./BaseComponent";
import { Manager, OrderId } from "@Root/types";
import { IFine, FineItem } from "@Root/reducers/fine";
import FineAction from "@Root/actions/FineAction";
import Select, { SelectItem } from "@Root/components/Select";
import { MONTHS } from "@Root/consts";
import Tools from "@Root/tools/Tools";
import CommonTools from "@Root/tools/CommonTools";

class Fine extends BaseComponent<IMapStateToProps>
{
    componentDidMount(){
        FineAction.load(this.props.order_id)
    }

    changeSelect(name: "manager_responsible" | "month", item: FineItem, select: SelectItem){
        FineAction.change(this.props.order_id, item.id, name, select.val)
    }

    changeFine(item: FineItem, e: Event){
        let target = e.target as HTMLInputElement
        let val = target.value.replace(/\s+/, "")
        let fine = parseInt(val)
        if( isNaN(fine) || fine <= 0 ){
            target.value = Tools.fnum(item.fine)
            return
        }

        FineAction.change(this.props.order_id, item.id, "fine", fine)
    }

    changeComment(item: FineItem, e: Event){
        let val = (e.target as HTMLInputElement).value
        FineAction.change(this.props.order_id, item.id, "reason", val)
    }

    addRow(){
        FineAction.create(this.props.order_id)
    }

    remove(item: FineItem){
        if( confirm("Удалить штраф?") )
            FineAction.delete(this.props.order_id, item.id)
    }

    getItems(){
        return this.props.items.map(item => {
            let initiator = CommonTools.getManager(item.manager_initiator)

            return <tr class="table__tr">
            <td class="table__td">
                <div class="form__item form__field form__field_size-m_s">
                    <input type="text" value={item.date} readOnly class="form__input" />
                </div>
            </td>
            <td class="table__td">
                <div class="form__item form__field form__field_size-xl_xs">
                    <input type="text" value={initiator} readOnly class="form__input" />
                </div>
            </td>
            <td class="table__td">
                <div class="form__item order__field order__field_size-fn_xs">
                    <input type="text" value={Tools.fnum(item.fine)} class="form__input" onChange={this.changeFine.bind(this, item)} />
                </div>
            </td>
            <td class="table__td">
                <div class="form__item form__field form__field_size-xl_xs">
                    <Select key={Select.key()} items={this.props.managers.map(manager => ({val: manager.id, title: manager.name}))} default={{title: "-", val: ""}}
                        class="js-select form__select" onSelect={this.changeSelect.bind(this, "manager_responsible", item)} value={item.manager_responsible} />
                </div>
            </td>
            <td class="table__td">
                <div class="form__item order__field order__field_size-fn_xxxl">
                    <input type="text" value={item.reason} class="form__input" onChange={this.changeComment.bind(this, item)}/>
                </div>
            </td>
            <td class="table__td">
                <div class="form__item order__field order__field_size-fn_l">
                    <Select key={Select.key()} items={Tools.getMonthsArr()} class="js-select form__select" onSelect={this.changeSelect.bind(this, "month", item)} value={item.month} />
                </div>
            </td>
            <td class="table__td table__td_align_right">
                <div class="form__item order__field order__field_size-fn_xs">

                    <div class="link link_icon_big " onClick={this.remove.bind(this, item)}>
                    <SvgIcon name="icon_delete" class="link__icon"/>&nbsp;</div>
                </div>
            </td>
        </tr>
        })
    }

    render() {
        return <div>
            <div class="table form" id="fineTable">
                <table class="table__main">
                    <thead class="table__thead">
                        <tr class="table__tr">
                            <th class="table__th">Дата</th>
                            <th class="table__th">Инициатор</th>
                            <th class="table__th">Сумма</th>
                            <th class="table__th">Ответственный</th>
                            <th class="table__th">Основние</th>
                            <th class="table__th">Месяц ЗП</th>
                            <th class="table__th">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody class="table__tbody">
                        {this.getItems()}
                    </tbody>
                </table>
            </div>
            <div class="panel__foot panel__foot_type_full">
                <div class="panel__main">
                    <button class="button button_type_concrete" type="button" onClick={this.addRow.bind(this)}>Добавить штраф</button>
                </div>
            </div>
        </div>
    }
}


type IMapStateToProps = IFine & {currentManager: number} & OrderId & {managers: Manager[]}

const mapStateToProps = (state: IRootReducer): IMapStateToProps => {
    return {...state.fine, currentManager: state.order.currentManager, order_id: state.order.order_id, managers: state.order.managers}
};

export default connect(mapStateToProps)(Fine)