import { h, Component } from "preact";
import { IRootReducer } from "@Root/reducers";
import { connect } from "preact-redux";
import SvgIcon from "@Components/SvgIcon"
import BaseComponent from "./BaseComponent";
import { Manager, OrderId } from "@Root/types";
import { IDeduction, DeductionItem } from "@Root/reducers/deduction";
import DeductionAction from "@Root/actions/DeductionAction";
import Select, { SelectItem } from "@Root/components/Select";
import Tools from "@Root/tools/Tools";
import CommonTools from "@Root/tools/CommonTools";

class Deduction extends BaseComponent<IMapStateToProps>
{
    async componentDidMount(){
        await DeductionAction.loadTypes()
        DeductionAction.load(this.props.order_id)
    }

    changeType(item: DeductionItem, select: SelectItem){
        DeductionAction.change(this.props.order_id, item.id, "type", select.val)
    }

    changeDeduction(item: DeductionItem, e: Event){
        let target = e.target as HTMLInputElement
        let val = target.value.replace(/\s+/, "")
        let fine = parseInt(val)
        if( isNaN(fine) || fine <= 0 ){
            target.value = Tools.fnum(item.deduction)
            return
        }

        DeductionAction.change(this.props.order_id, item.id, "deduction", fine)
    }

    changeComment(item: DeductionItem, e: Event){
        let val = (e.target as HTMLInputElement).value
        DeductionAction.change(this.props.order_id, item.id, "comment", val)
    }

    addRow(){
        DeductionAction.create(this.props.order_id)
    }

    remove(item: DeductionItem){
        if( confirm("Удалить вычет?") )
            DeductionAction.delete(this.props.order_id, item.id)
    }

    getItems(){
        return this.props.items.map(item => {
            let initiator = CommonTools.getManager(item.manager)

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
                    <input type="text" value={Tools.fnum(item.deduction)} class="form__input" onChange={this.changeDeduction.bind(this, item)} />
                </div>
            </td>
            <td class="table__td">
                <div class="form__item form__field form__field_size-xl_xs">
                    <Select key={Select.key()} items={this.props.types} class="js-select form__select" onSelect={this.changeType.bind(this, item)} value={item.type} />
                </div>
            </td>
            <td class="table__td">
                <div class="form__item order__field order__field_size-fn_xxxl">
                    <input type="text" value={item.comment} class="form__input" onChange={this.changeComment.bind(this, item)}/>
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
                            <th class="table__th">Менеджер</th>
                            <th class="table__th">Сумма</th>
                            <th class="table__th">Тип</th>
                            <th class="table__th">Комментарий</th>
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
                    <button class="button button_type_concrete" type="button" onClick={this.addRow.bind(this)}>Добавить вычет</button>
                </div>
            </div>
        </div>
    }
}


type IMapStateToProps = IDeduction & {currentManager: number} & OrderId & {managers: Manager[]}

const mapStateToProps = (state: IRootReducer): IMapStateToProps => {
    return {...state.deduction, currentManager: state.order.currentManager, order_id: state.order.order_id, managers: state.order.managers}
};

export default connect(mapStateToProps)(Deduction)