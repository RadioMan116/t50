import { Component, h } from "preact";
import { IRootReducer } from "@Root/reducers";
import { connect } from "preact-redux";
import SvgIcon from "@Components/SvgIcon"
import BaseComponent from "./BaseComponent";
import Checkbox from "@Components/Checkbox";
import { IProfit, ProfitBasketItem, ProfitInstalItem } from "@Root/reducers/profit";
import { OrderId, Month, Manager } from "@Root/types";
import Tools from "@Root/tools/Tools";
import Select, { SelectItem } from "@Root/components/Select";
import ProfitTools from "@Root/tools/ProfitTools";
import ProfitAction, { AccountMonth } from "@Root/actions/ProfitAction";
import CommonTools from "@Root/tools/CommonTools";

class Profit extends BaseComponent<IMapStateToProps>
{
    componentDidUpdate() {
        super.componentDidUpdate();
        this.jqueryUpdate = false;
    }

    setBasketSupplierCommission(item: ProfitBasketItem, e: Event){
        let value = parseInt((e.target as HTMLInputElement).value)
        ProfitAction.setBasketSupplierCommission(item, value)
    }

    setInstalSupplierCommission(item: ProfitInstalItem, e: Event){
        let value = parseInt((e.target as HTMLInputElement).value)
        ProfitAction.setInstalSupplierCommission(item, value)
    }

    setBasketAccountMonth(code: AccountMonth, item: ProfitBasketItem, selItem: SelectItem){
        ProfitAction.setBasketAccountMonth(code, item, selItem.val as Month)
    }

    setInstalAccountMonth(code: AccountMonth, item: ProfitInstalItem, selItem: SelectItem){
        ProfitAction.setInstalAccountMonth(code, item, selItem.val as Month)
    }

    setShareManager(selItem: SelectItem, isCheckbox = false){
        if( isCheckbox && !(this.props.share_com_manager > 0) )
            return false;

        ProfitAction.setShareManager(selItem.val as number)
        return true;
    }

    renderSaleTable() {
        return <div class="table form">
            <table class="table__main">
                <thead class="table__thead">
                    <tr class="table__tr">
                        <th class="table__th">Наименование</th>
                        <th class="table__th">Поставщик</th>
                        <th class="table__th">№ заказа</th>
                        <th class="table__th">Дата отгрузки</th>
                        <th class="table__th">Цена продажи</th>
                        <th class="table__th">Цена закупки</th>
                        <th class="table__th">Логистика</th>
                        <th class="table__th">Комиссия наша</th>
                        <th class="table__th">Комиссия пост.</th>
                        <th class="table__th">Разница</th>
                        {/* <th class="table__th">

                            <div class="sort">
                                <div class="sort__wrapper">Разница</div>
                                <div class="sort__trigger">&nbsp;</div>
                                <div class="sort__dropdown">
                                    <div class="sort__label">Сортировка</div>
                                    <div class="sort__item"><button type="button" data-sort="[[10,0]]" class="sort__link-trigger js-table-sort-trigger">По
                                возрастанию</button></div>
                                    <div class="sort__item"><button type="button" data-sort="[[10,1]]" class="sort__link-trigger js-table-sort-trigger">По
                                убыванию</button></div>
                                    <div class="sort__item"><button type="button" data-sort="data-sort" class="sort__link-trigger js-table-sort-trigger">Отключить</button></div>
                                </div>
                            </div>
                        </th> */}
                        <th class="table__th">Месяц учета ВП</th>
                        <th class="table__th">Месяц учета ЗП</th>
                    </tr>
                </thead>
                <tbody class="table__tbody">
                    {this.props.basket_items.map(item => (
                        <tr class={"table__tr" + (item.is_claim ? " table__tr_state_disabled" : "")}>
                            <td class="table__td">
                                <div class="table__td-disabled">
                                    <div class="form__field form__field_size-xl_m">
                                        <a class="link link_styel_classic" href={item.url}>{item.title}</a>
                                    </div>
                                </div>
                            </td>
                            <td class="table__td">
                                <div class="table__td-disabled">
                                    <div class="form__field form__field_size-m_xs">{item.supplier_name}</div>
                                </div>
                            </td>
                            <td class="table__td">
                                <div class="table__td-disabled">
                                    <div class="form__field form__field_size-m_xs">{item.account}</div>
                                </div>
                            </td>
                            <td class="table__td">
                                <div class="table__td-disabled">
                                    <div class="form__field form__field_size-m_xs">{item.date}</div>
                                </div>
                            </td>
                            <td class="table__td">
                                <div class="table__td-disabled">
                                    <div class="form__field form__field_size-m_xs">{Tools.fnum(item.sale)}</div>
                                </div>
                            </td>
                            <td class="table__td">
                                <div class="table__td-disabled">
                                    <div class="form__field form__field_size-m_xs">{Tools.fnum(item.purchase)}</div>
                                </div>
                            </td>
                            <td class="table__td">
                                <div class="table__td-disabled">
                                    <div class="order__field order__field_size_m-xs">{Tools.fnum(item.logistics)}</div>
                                </div>
                            </td>
                            <td class="table__td">
                                <div class="table__td-disabled">
                                    <div class="form__field form__field_size-m_xs">{Tools.fnum(item.commission)}</div>
                                </div>
                            </td>
                            <td class="table__td">
                                <div class="table__td-disabled">
                                    <div class="form__field form__field_size-m_xs">
                                        <input type="text" value={item.suppl_commission} class="form__input" onChange={this.setBasketSupplierCommission.bind(this, item)}/>
                                    </div>
                                </div>
                            </td>
                            <td class="table__td">
                                <div class="table__td-disabled">
                                    <div class="form__field form__field_size-m_s">{Tools.fnum(item.diff)}</div>
                                </div>
                            </td>
                            <td class="table__td">
                                <div class="form__field form__field_size-m_s">
                                    <Select key={Select.key()} items={ProfitTools.getMonths()} value={item.mounth_vp} onSelect={this.setBasketAccountMonth.bind(this, "month_vp", item)} class="js-select form__select" />
                                </div>
                            </td>
                            <td class="table__td">
                                <div class="form__field form__field_size-m_s">
                                    <Select key={Select.key()} items={ProfitTools.getMonths()} value={item.mounth_zp} onSelect={this.setBasketAccountMonth.bind(this, "month_zp", item)} class="js-select form__select" />
                                </div>
                            </td>
                        </tr>
                    ))}

                    <tr class="table__tr">
                        <td class="table__td table__td_valign_bottom">
                            <div class="table__td-disabled">
                                <div class="table__marked">Итого:</div>
                            </div>
                        </td>
                        <td class="table__td table__td_valign_bottom">
                            <div class="table__td-disabled">&nbsp;</div>
                        </td>
                        <td class="table__td table__td_valign_bottom">
                            <div class="table__td-disabled">&nbsp;</div>
                        </td>
                        <td class="table__td table__td_valign_bottom">
                            <div class="table__td-disabled">&nbsp;</div>
                        </td>
                        <td class="table__td table__td_valign_bottom">
                            <div class="table__td-disabled">
                                <div class="marked status_failed">{Tools.fnum(this.props.basket_sum.sale)}</div>
                            </div>
                        </td>
                        <td class="table__td table__td_valign_bottom">
                            <div class="table__td-disabled">
                                <div class="marked status_failed">{Tools.fnum(this.props.basket_sum.purchase)}</div>
                            </div>
                        </td>
                        <td class="table__td table__td_valign_bottom">
                            <div class="table__td-disabled">
                                <div class="marked status_failed">{Tools.fnum(this.props.basket_sum.logistics)}</div>
                            </div>
                        </td>
                        <td class="table__td table__td_valign_bottom">
                            <div class="table__td-disabled">
                                <div class="marked status_failed">{Tools.fnum(this.props.basket_sum.commission)}</div>
                            </div>
                        </td>
                        <td class="table__td table__td_valign_bottom">
                            <div class="table__td-disabled">
                                <div class="marked status_failed">{Tools.fnum(this.props.basket_sum.suppl_commission)}</div>
                            </div>
                        </td>
                        <td class="table__td table__td_valign_bottom">
                            <div class="table__td-disabled">
                                <div class="marked status_failed">{Tools.fnum(this.props.basket_sum.diff)}</div>
                            </div>
                        </td>
                        <td class="table__td table__td_valign_bottom">&nbsp;</td>
                        <td class="table__td table__td_valign_bottom">&nbsp;</td>
                    </tr>
                </tbody>
            </table>
        </div>
    }

    renderInstallationTable() {
        return <div class="table form" >
            <table class="table__main">
                <thead class="table__thead">
                    <tr class="table__tr">
                        <th class="table__th">Наименование</th>
                        <th class="table__th">Поставщик</th>
                        <th class="table__th">№ заказа</th>
                        <th class="table__th">Дата отгрузки</th>
                        <th class="table__th">Цена продажи</th>
                        <th class="table__th">Цена закупки</th>
                        <th class="table__th">Выезд</th>
                        <th class="table__th">Логистика</th>
                        <th class="table__th">Комиссия наша</th>
                        <th class="table__th">Комиссия пост.</th>
                        <th class="table__th">Разница</th>
                        {/* <th class="table__th">

                            <div class="sort">
                                <div class="sort__wrapper">Разница</div>
                                <div class="sort__trigger">&nbsp;</div>
                                <div class="sort__dropdown">
                                    <div class="sort__label">Сортировка</div>
                                    <div class="sort__item"><button type="button" data-sort="[[10,0]]" class="sort__link-trigger js-table-sort-trigger">По
                                возрастанию</button></div>
                                    <div class="sort__item"><button type="button" data-sort="[[10,1]]" class="sort__link-trigger js-table-sort-trigger">По
                                убыванию</button></div>
                                    <div class="sort__item"><button type="button" data-sort="data-sort" class="sort__link-trigger js-table-sort-trigger">Отключить</button></div>
                                </div>
                            </div>
                        </th> */}
                        <th class="table__th">Месяц учета ВП</th>
                        <th class="table__th">Месяц учета ЗП</th>
                    </tr>
                </thead>
                <tbody class="table__tbody">
                    {this.props.instal_items.map(item => (
                        <tr class={"table__tr" + (item.is_claim ? " table__tr_state_disabled" : "")}>
                            <td class="table__td">
                                <div class="table__td-disabled">
                                    <div class="form__field form__field_size-xl_m">
                                        <a class="link link_styel_classic" href={item.url}>{item.title}</a>
                                    </div>
                                </div>
                            </td>
                            <td class="table__td">
                                <div class="table__td-disabled">
                                    <div class="form__field form__field_size-s_xs">
                                        {CommonTools.getSelectItem(item.provider, this.props.installation_providers) ?? "-"}
                                    </div>
                                </div>
                            </td>
                            <td class="table__td">
                                <div class="table__td-disabled">
                                    <div class="form__field form__field_size-s_xs">{item.account}</div>
                                </div>
                            </td>
                            <td class="table__td">
                                <div class="table__td-disabled">
                                    <div class="form__field form__field-_sizes_xs">{item.date}</div>
                                </div>
                            </td>
                            <td class="table__td">
                                <div class="table__td-disabled">
                                    <div class="form__field form__field_size-s_xs">{Tools.fnum(item.sale)}</div>
                                </div>
                            </td>
                            <td class="table__td">
                                <div class="table__td-disabled">
                                    <div class="form__field form__field_size-s_xs">{Tools.fnum(item.purchase)}</div>
                                </div>
                            </td>
                            <td class="table__td">
                                <div class="table__td-disabled">
                                    <div class="form__field form__field_size-s_xs">{Tools.fnum(item.master)}</div>
                                </div>
                            </td>
                            <td class="table__td">
                                <div class="table__td-disabled">
                                    <div class="form__field form__field_size-s_xs">{Tools.fnum(item.logistics)}</div>
                                </div>
                            </td>
                            <td class="table__td">
                                <div class="table__td-disabled">
                                    <div class="form__field form__field_size-s_xs">{Tools.fnum(item.commission)}</div>
                                </div>
                            </td>
                            <td class="table__td">
                                <div class="table__td-disabled">
                                    <div class="form__field form__field_size-s_xs">
                                        <input type="text" value={Tools.fnum(item.suppl_commission)} class="form__input" onChange={this.setInstalSupplierCommission.bind(this, item)}/>
                                    </div>
                                </div>
                            </td>
                            <td class="table__td">
                                <div class="table__td-disabled">
                                    <div class="form__field form__field_size-m_s">{Tools.fnum(item.diff)}</div>
                                </div>
                            </td>
                            <td class="table__td">
                                <div class="form__field form__field_size-m_s">
                                    <Select key={Select.key()} items={ProfitTools.getMonths()} value={item.mounth_vp} onSelect={this.setInstalAccountMonth.bind(this, "month_vp", item)} class="js-select form__select" />
                                </div>
                            </td>
                            <td class="table__td">
                                <div class="form__field form__field_size-m_s">
                                    <Select key={Select.key()} items={ProfitTools.getMonths()} value={item.mounth_zp} onSelect={this.setInstalAccountMonth.bind(this, "month_zp", item)} class="js-select form__select" />
                                </div>
                            </td>
                        </tr>
                    ))}
                </tbody>
                <tfoot class="table__tfoot">
                    <tr class="table__tr table__tr_style_ninja">
                        <td class="table__td table__td_valign_bottom">
                            <div class="table__td-disabled">
                                <div class="table__marked">Итого:</div>
                            </div>
                        </td>
                        <td class="table__td table__td_valign_bottom">
                            <div class="table__td-disabled">
                                <div class="form__field form__field_size-s_xs">&nbsp;</div>
                            </div>
                        </td>
                        <td class="table__td table__td_valign_bottom">
                            <div class="table__td-disabled">
                                <div class="form__field form__field_size-s_xs">&nbsp;</div>
                            </div>
                        </td>
                        <td class="table__td table__td_valign_bottom">
                            <div class="table__td-disabled">
                                <div class="form__field form__field_size-s_xs">&nbsp;</div>
                            </div>
                        </td>
                        <td class="table__td table__td_valign_bottom">
                            <div class="table__td-disabled">
                                <div class="form__field form__field_size-s_xs">
                                    <div class="marked status_failed">{Tools.fnum(this.props.instal_sum.sale)}</div>
                                </div>
                            </div>
                        </td>
                        <td class="table__td table__td_valign_bottom">
                            <div class="table__td-disabled">
                                <div class="form__field form__field_size-s_xs">
                                    <div class="marked status_failed">{Tools.fnum(this.props.instal_sum.purchase)}</div>
                                </div>
                            </div>
                        </td>
                        <td class="table__td table__td_valign_bottom">
                            <div class="table__td-disabled">
                                <div class="form__field form__field_size-s_xs">
                                    <div class="marked status_failed">{Tools.fnum(this.props.instal_sum.master)}</div>
                                </div>
                            </div>
                        </td>
                        <td class="table__td table__td_valign_bottom">
                            <div class="table__td-disabled">
                                <div class="form__field form__field_size-s_xs">
                                    <div class="marked status_failed">{Tools.fnum(this.props.instal_sum.logistics)}</div>
                                </div>
                            </div>
                        </td>
                        <td class="table__td table__td_valign_bottom">
                            <div class="table__td-disabled">
                                <div class="form__field form__field_size-s_xs">
                                    <div class="marked status_failed">{Tools.fnum(this.props.instal_sum.commission)}</div>
                                </div>
                            </div>
                        </td>
                        <td class="table__td table__td_valign_bottom">
                            <div class="table__td-disabled">
                                <div class="form__field form__field_size-m_s">
                                    <div class="marked status_failed">{Tools.fnum(this.props.instal_sum.suppl_commission)}</div>
                                </div>
                            </div>
                        </td>
                        <td class="table__td table__td_valign_bottom">
                            <div class="table__td-disabled">
                                <div class="form__field form__field_size-m_s">
                                    <div class="marked status_failed">{Tools.fnum(this.props.instal_sum.diff)}</div>
                                </div>
                            </div>
                        </td>
                        <td class="table__td table__td_valign_bottom">
                            <div class="form__field form__field_size-m_s">&nbsp;</div>
                        </td>
                        <td class="table__td table__td_valign_bottom">
                            <div class="form__field form__field_size-m_s">&nbsp;</div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    }

    renderManagers() {
        return <div class="table table_style_simple table_width_auto">
            <table class="table__main">
                <thead class="table__thead">
                    <tr class="table__tr">
                        <th class="table__th table__th_close_bottom">Делить комиссию</th>
                        <th class="table__th table__th_close_bottom"> Укажите менеджера</th>
                    </tr>
                </thead>
                <tbody class="table__tbody">
                    <tr class="table__tr">
                        <td class="table__td table__td_close_bottom">
                            <div class="form__line form__line_type_close form__field form__field_size-m_s">
                                <Checkbox checked={this.props.share_com_manager > 0} text={(this.props.share_com_manager > 0 ? "Да" : "Нет")} onClick={this.setShareManager.bind(this, {title: "", val: 0}, true)} awaitConfirm={true}/>
                            </div>
                        </td>
                        <td class="table__td table__td_close_bottom">
                            <div class="form__line form__line_type_close form__field form__field_size-xl_m">
                                <Select key={Select.key()} items={CommonTools.getManagersForSelection()} onSelect={this.setShareManager.bind(this)} value={this.props.share_com_manager} class="js-select form__select" default={{title: "", val: 0}}/>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    }


    render() {
        return <div>
            <span class="panel__title panel__title_type_subtitle">Продажа бытовой техники</span>
            <div class="panel__item">{this.renderSaleTable()}</div>
            <div class="panel__place panel__item">{this.renderManagers()}</div>
            <h3 class="panel__title panel__title_type_subtitle">Продажа установки бытовой техники</h3>
            {this.renderInstallationTable()}
        </div>
    }
}

type IMapStateToProps = IProfit & {share_com_manager: number} & {installation_providers: SelectItem[]}

const mapStateToProps = (state: IRootReducer): IMapStateToProps => {
    return {
        ...state.profit,
        share_com_manager: state.order.share_com_manager,
        installation_providers: state.order.installationProviders
    }
}

export default connect(mapStateToProps)(Profit)