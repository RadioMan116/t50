import { Component, h } from "preact";
import { IRootReducer } from "@Root/reducers";
import { connect } from "preact-redux";
import SvgIcon from "@Components/SvgIcon";
import BaseComponent from "./BaseComponent";
import Checkbox from "@Components/Checkbox";
import { IAccounts, AccountsItem } from "@Root/reducers/accounts";
import Select, { SelectItem } from "@Root/components/Select";
import AccountAction from "@Root/actions/AccountAction";
import DateTimePicker from "@Root/components/DateTimePicker";
import AccountsTools from "@Root/tools/AccountsTools";
import { OrderId } from "@Root/types";
import BasketTools from "@Root/tools/BasketTools";
import Comparator from "@Root/tools/Comparator";

export const MulipleProps = ["account_product", "date_arrival", "account_delivery", "official_our", "official_partners", "account_tn_tk"]
type CountWithChildForItem = {[itemId: number] : number}
class Accounts extends BaseComponent<IMapStateToProps>
{
    state: {cnt:CountWithChildForItem} = {cnt: {}}

    componentDidUpdate(){
        super.componentDidUpdate();
        this.jqueryUpdate = false;
    }

    componentWillReceiveProps(props:IMapStateToProps){
        let cnt: CountWithChildForItem = {};
        props.items.forEach(item => {
            cnt[item.id] = this.searchMaxInMultiple(item)
        })
        this.setState({cnt})
    }

    changeInput(item: AccountsItem, index: number, name: keyof AccountsItem, e: Event) {
        let oldVal = item[name]
        let target = e.target as HTMLInputElement
        let val = AccountsTools.setArrayValue(item[name] as string|string[], target.value, index);
        if( !AccountsTools.isEquals(oldVal as string, val) ){
            AccountAction.setValue(this.props.order_id, item.basket_id, name, val)
        }
    }

    changeSupplier(item: AccountsItem, select: SelectItem){
        AccountAction.setSupplier(this.props.order_id, item.basket_id, select.val)
    }

    changeCheckbox(item: AccountsItem, name: "in_stock" | "shipment", val: boolean){
        AccountAction.setValue(this.props.order_id, item.basket_id, name, val)
    }

    addRow(id: number){
        let cnt = {...this.state.cnt}
        cnt[id] ++
        this.setState({cnt})
    }

    deleteRow(item: AccountsItem, index: number){
        if( !confirm("Удалить все данные в строке?") )
            return

        AccountAction.removeRow(this.props.order_id, item.basket_id, index)
    }

    searchMaxInMultiple(item: AccountsItem) {
        let mulipleProps = MulipleProps.map(prop => item[prop].length)
        let max = Math.max.apply(null, mulipleProps);
        return max ? max : 1;
    }

    prepareItem(item: AccountsItem) {
        let basket = BasketTools.getById(item.basket_id)

        let rows = []
        let max = this.state.cnt[item.id]
        if( max == 0 )
            max = 1

        for (let i = 0; i < max; i++) {
            let className = "table__tr"
            if( max > 1 ){
                className += (i == 0 ? " table__tr_type_parent" : " table__tr_type_child")
                if( i == max - 1)
                    className += " table__tr_type_last"
            }
            if( basket?.claim )
                className += " table__tr_state_disabled"

            if( basket?.claim_replace && i == 0 )
                className += " table__tr_style_marked "

            rows.push(<tr class={className}>
                <td class="table__td">
                    {i == 0 ?
                    <div class="table__td-disabled">
                        <div class="form__field form__field_size-xl_l">
                            <a class="link link_styel_classic" href={basket?.url}>{basket?.title}</a>
                        </div>
                    </div>
                    : " "}
                </td>
                <td class="table__td">
                    {i == 0 ?
                    <div class="table__td-disabled">
                        <div class="form__field form__field_size-m_l">
                            <Select key={Select.key()} items={this.props.suppliers} value={basket?.supplier_id} default={{ val: 0, title: "-" }} onSelect={this.changeSupplier.bind(this, item)} class="js-select form__select" />
                        </div>
                    </div>
                    : " "}
                </td>
                <td class="table__td">
                    {i == 0 ?
                    <div class="table__td-disabled">
                        <div class="form__field form__field_size-m_s">
                            <div class="form__item">
                                <input type="text" value={item.account} class="form__input" onChange={this.changeInput.bind(this, item, i, "account")} />
                            </div>
                        </div>
                    </div>
                    : " "}
                </td>
                <td class="table__td">
                    <div class="table__td-disabled">
                        <div class="form__field form__field_size-m_s">
                            <div class="form__item">
                                <input type="text" value={item.account_product[i]} class="form__input" onChange={this.changeInput.bind(this, item, i, "account_product")}/>
                            </div>
                        </div>
                    </div>
                </td>
                <td class="table__td">
                    <div class="table__td-disabled">
                        <div class="form__field form__field_size-m_s">
                            <div class="form__item">
                                <DateTimePicker type="date" value={item.date_arrival[i]} showIcon={false} onChange={this.changeInput.bind(this, item, i, "date_arrival")}/>
                            </div>
                        </div>
                    </div>
                </td>
                <td class="table__td">
                    <div class="table__td-disabled">
                        <div class="form__field form__field_size-m_s">
                            <div class="form__item">
                                <input type="text" value={item.account_delivery[i]} class="form__input" onChange={this.changeInput.bind(this, item, i, "account_delivery")}/>
                            </div>
                        </div>
                    </div>
                </td>
                <td class="table__td">
                    <div class="table__td-disabled">
                        <div class="form__field form__field_size-s_xs">
                            <div class="form__item">
                                <input type="text" value={item.official_our[i]} class="form__input" onChange={this.changeInput.bind(this, item, i, "official_our")}/>
                            </div>
                        </div>
                    </div>
                </td>
                <td class="table__td">
                    <div class="table__td-disabled">
                        <div class="form__field form__field_size-m_s">
                            <div class="form__item">
                                <input type="text" value={item.official_partners[i]} class="form__input" onChange={this.changeInput.bind(this, item, i, "official_partners")}/>
                            </div>
                        </div>
                    </div>
                </td>
                <td class="table__td">
                    <div class="table__td-disabled">
                        <div class="form__field form__field_size-m_xs">
                            <div class="form__item">
                                <input type="text" value={item.account_tn_tk[i]} class="form__input" onChange={this.changeInput.bind(this, item, i, "account_tn_tk")}/>
                            </div>
                        </div>
                    </div>
                </td>
                <td class="table__td">
                    {i == 0 ?
                    <div class="form__field">
                        <Checkbox checked={item.in_stock} onClick={this.changeCheckbox.bind(this, item, "in_stock")}/>
                    </div>
                    : " "}
                </td>
                <td class="table__td">
                    {i == 0 ?
                    <div class={"form__field " + (basket?.claim_replace && i == 0 ? /* " order__top-connect" */ "" : "")}>
                        <Checkbox checked={item.shipment} onClick={this.changeCheckbox.bind(this, item, "shipment")}/>
                    </div>
                    : " "}
                </td>
                <td class="table__td table__td_align_right">
                    {i == 0 ?
                    <div class="table__td-disabled">
                        <div class="link link_icon_big" onClick={this.addRow.bind(this, item.id)}>
                            <SvgIcon name="icon_add" class="link__icon" />&nbsp;
                        </div>
                    </div>
                    :
                    <div class="table__td-disabled">
                        <div class="link link_icon_big" onClick={this.deleteRow.bind(this, item, i)}>
                            <SvgIcon name="icon_delete" class="link__icon" />&nbsp;
                        </div>
                    </div>
                    }
                </td>
            </tr>)
        }

        return rows
    }

    shouldComponentUpdate(nextProps: IMapStateToProps, nextState?: any){
        return ( !Comparator.isDeepEquals(this.props, nextProps) || !Comparator.isDeepEquals(this.state, nextState) );
    }

    render() {
        return <div class="table" id="nszTable">
            <table class="table__main">
                <thead class="table__thead">
                    <tr class="table__tr">
                        <th class="table__th">Наименование</th>
                        <th class="table__th">Поставщик</th>
                        <th class="table__th">Заказ</th>
                        <th class="table__th">Заказ товара</th>
                        <th class="table__th">Дата прихода</th>
                        <th class="table__th">Заказ доставки</th>
                        <th class="table__th">Счет офиц. наш</th>
                        <th class="table__th">Счет офиц. партнеров</th>
                        <th class="table__th">Номер ТН ТК</th>
                        <th class="table__th">На складе</th>
                        <th class="table__th">Отгрузка</th>
                        <th class="table__th">&nbsp;</th>
                    </tr>
                </thead>
                <tbody class="table__tbody">
                    {this.props.items.map(this.prepareItem.bind(this))}
                </tbody>
            </table>
        </div>
    }
}


type IMapStateToProps = IAccounts & OrderId & {suppliers: SelectItem[]}

const mapStateToProps = (state: IRootReducer): IMapStateToProps => {
    return { ...state.accounts, order_id: state.order.order_id, suppliers: state.order.suppliers }
};

export default connect(mapStateToProps)(Accounts)