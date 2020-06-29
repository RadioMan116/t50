import { Component, h } from "preact";
import { IRootReducer } from "@Root/reducers";
import { connect } from "preact-redux";
import SvgIcon from "@Components/SvgIcon"
import BaseComponent from "./BaseComponent";
import RadioButtons from "@Root/components/RadioButtons";
import { IClient } from "@Root/reducers/client";
import ClientAction from "@Root/actions/ClientAction";
import Tools from "@Root/tools/Tools";
import Select, { SelectItem } from "@Root/components/Select";
import CommonTools from "@Root/tools/CommonTools";
import PhoneInput from "@Root/components/PhoneInput";

class Client extends BaseComponent<IMapStateToProps> {

    componentWillMount(){
        T50PubSub.subscribe("mailing_send", type => {
            if( type == "order_for_client" )
                ClientAction.load();
        });
    }

    onChangeInput(name: keyof IClient, e: Event){
        let target = e.target as HTMLInputElement
        this.changeAll(name, target.value)
    }

    setIsEntity(value:string){
        this.changeAll("is_entity", (value == "Y" ? "1" : "0") )
    }

    setElevator(code: string){
        this.changeAll("elevator", code)
    }

    changeAll(name: keyof IClient, value:string|boolean){
        ClientAction.change(this.props.id, name, value)
    }

    getPhoneLabel(value: string, text: string){
        if( !this.props.code_msk || !value )
            return <div class="form__label">{text}</div>

        let number = `${this.props.code_msk}${value.replace("+7", "8")}`
        number = number.replace(/[^\d]/g, "");
        return <div class="form__label"><a class="link link_styel_classic" href={`http://127.0.0.1:4059/switchto?number=${number}`}>{text}</a></div>
    }

    getCitySelect(){
        let orderCity = CommonTools.getSelectItem(this.props.order_city, this.props.cities)
        let value = ( this.props.city == orderCity ? this.props.order_city : null );
        let updCity = (city: SelectItem) => {
            ClientAction.setCity(city.val as number);
        }

        return <Select onSelect={updCity} items={this.props.cities} value={value} key={Select.key()}  class="js-select form__select"/>
    }

    render() {

        return <div>
            <div class="grid-12__row">
                <div class="grid-12__col grid-12__col_size_10">

                    <div class="table table_style_simple">
                        <table class="table__main">
                            <tbody class="table__tbody">
                                <tr class="table__tr">
                                    <td class="table__td table__td_valign_top">
                                        <label class="form__line order__field order__field_size_xxl">
                                            <div class="form__label">ФИО клиента</div>
                                            <input value={this.props.fio} onChange={this.onChangeInput.bind(this, "fio")} class="form__input" />
                                        </label>
                                    </td>
                                    <td class="table__td table__td_valign_top">
                                        <label class="form__line order__field order__field_size_m">
                                            {this.getPhoneLabel(this.props.phone, "Телефон (основной)")}
                                            <PhoneInput value={this.props.phone} onChange={this.changeAll.bind(this, "phone")} />
                                        </label>
                                    </td>
                                    <td class="table__td table__td_valign_top">
                                        <label class="form__line order__field order__field_size_l">
                                            {this.getPhoneLabel(this.props.phone_dop, "Телефон (дополнительный)")}
                                            <PhoneInput value={this.props.phone_dop} onChange={this.changeAll.bind(this, "phone_dop")} />
                                        </label>
                                    </td>
                                    <td class="table__td table__td_valign_top">
                                        <label class="form__line order__field order__field_size_l">
                                            <div class="form__label">ФИО к доп. телефону</div>
                                            <input value={this.props.fio_dop} onChange={this.onChangeInput.bind(this, "fio_dop")} class="form__input" />
                                        </label>
                                    </td>
                                    <td class="table__td table__td_valign_top">
                                        <label class="form__line order__field order__field_size_l ">
                                            <div class="form__label">E-mail</div>
                                            <input value={this.props.email} onChange={this.onChangeInput.bind(this, "email")} class="form__input" />
                                        </label>
                                    </td>
                                    <td class="table__td table__td_valign_top">
                                        <label class="form__line order__field form__field form__field_size-m_s">
                                            <div class="form__label">Тип клиента</div>

                                            <RadioButtons
                                                data={[{ value: "Y", title: "Юр. лицо" }, { value: "N", title: "Физ. лицо" }]}
                                                itemClass="table__item"
                                                name="is_entity"
                                                value={this.props.is_entity ? "Y" : "N"}
                                                onChange={this.setIsEntity.bind(this)}
                                            />
                                        </label>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="table table_style_simple table_width_auto">
                        <table class="table__main">
                            <tbody class="table__tbody">
                                <tr class="table__tr">
                                    <td class="table__td">
                                        <label class="form__line order__field order__field_size_l">
                                            <div class="form__label">Город</div>
                                            {this.getCitySelect()}
                                            {/* <input value={this.props.city} onChange={this.onChangeInput.bind(this, "city")} class="form__input" /> */}
                                        </label>
                                    </td>
                                    <td class="table__td">
                                        <label class="form__line order__field order__field_size_l">
                                            <div class="form__label">Улица</div>
                                            <input value={this.props.street} onChange={this.onChangeInput.bind(this, "street")} class="form__input" />
                                        </label>
                                    </td>
                                    <td class="table__td">
                                        <label class="form__line order__field order__field_size_xs">
                                            <div class="form__label">Дом</div>
                                            <input value={this.props.house_number} onChange={this.onChangeInput.bind(this, "house_number")} class="form__input" />
                                        </label>
                                    </td>
                                    <td class="table__td">
                                        <label class="form__line order__field order__field_size_xs">
                                            <div class="form__label">Подъезд</div>
                                            <input value={this.props.porch} onChange={this.onChangeInput.bind(this, "porch")} class="form__input" />
                                        </label>
                                    </td>
                                    <td class="table__td">
                                        <label class="form__line order__field order__field_size_xs">
                                            <div class="form__label">Этаж</div>
                                            <input value={this.props.floor} onChange={this.onChangeInput.bind(this, "floor")} class="form__input" />
                                        </label>
                                    </td>
                                    <td class="table__td">
                                        <label class="form__line order__field order__field_size_xs">
                                            <div class="form__label">Квартира</div>
                                            <input value={this.props.apartment} onChange={this.onChangeInput.bind(this, "apartment")} class="form__input" />
                                        </label>
                                    </td>
                                    <td class="table__td">
                                        <label class="form__line order__field order__field_size_xs">
                                            <div class="form__label">Домофон</div>
                                            <input value={this.props.intercom} onChange={this.onChangeInput.bind(this, "intercom")} class="form__input" />
                                        </label>
                                    </td>
                                    <td class="table__td">
                                        <div class="form__line">
                                            <div class="form__label">Лифт</div>
                                            <div class="table__checks-panel">
                                                <div class="dual-panel">
                                                    <div class="dual-panel__row">
                                                        <RadioButtons
                                                            data={[
                                                                { value: "freight", title: "Грузовой" },
                                                                { value: "passenger", title: "Пассажирский" },
                                                                { value: "not", title: "Нет" },
                                                            ]}
                                                            itemClass="dual-panel__col"
                                                            name="elevator"
                                                            value={Tools.alt(this.props.elevator, "not")}
                                                            onChange={this.setElevator.bind(this)}
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="grid-12__col grid-12__col_size_2">
                    <div class="table table_style_simple">
                        <table class="table__main">
                            <tbody class="table__tbody">
                                <td class="table__td table__td_valign_top">
                                    {this.props.is_entity ?
                                        <label class="form__line ur-person">
                                            <div class="form__label">Реквизиты</div>
                                            <textarea class="form__textarea form__textarea_size_regular" value={this.props.requisites} onChange={this.onChangeInput.bind(this, "requisites")} ></textarea>
                                        </label>
                                        :
                                        <label class="form__line fz-person ">
                                            <div class="form__label">Паспорт (Серия номер)</div>
                                            <input class="form__input" value={this.props.requisites} onChange={this.onChangeInput.bind(this, "requisites")} />
                                        </label>
                                    }
                                </td>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="panel__foot panel__foot_type_full">
                <div class="panel__main">
                    <a class={"js-modal link link_type_email " + ( this.props.send_email ? "link_icon_marked" : "" )}
                        title={(this.props.send_email ? "отправлено" : "")}
                        href="#email_editor" data-email_type="order_for_client" data-order_id={this.props.order_id}>
                        <SvgIcon name="icon_e-mail" class="link__icon" />
                        Отправить на почту клиенту
                    </a>
                </div>
            </div>
        </div>
    }
}


type IMapStateToProps = IClient & {
    code_msk?: string,
    code_spb?: string,
    order_id: number,
    cities: SelectItem[],
    order_city: number,
}


const mapStateToProps = (state: IRootReducer): IMapStateToProps => ({
    ...state.client,
    code_msk: state.order.phone_code_msk,
    code_spb: state.order.phone_code_spb,
    order_id: state.order.order_id,
    cities: state.order.cities,
    order_city: state.order.city
});

export default connect(mapStateToProps)(Client)