import { Component, h } from "preact";
import SvgIcon from "./SvgIcon";
import Tools from "@Root/tools/Tools";
import { PriceComment } from "@Root/types";

type ManualKey = "basket_sale" | "basket_purchase" | "install_sale" | "install_purchase"
interface Props {
    manualKey: ManualKey,
    value: number,
    isManual: boolean,
    order_id: number,
    id: number,
    comment: PriceComment
}
export default class Hand extends Component<Props>
{
    static uniqueId = 0;

    render() {
        let uniqueId = `tooltip_nahd_${++Hand.uniqueId}`;

        return <div class="table__td-disabled">
            <div class="form__field form__field_size-l_xs">

                <div class="handbrake-group">
                    <div class="handbrake-group__content">
                        <div class="form__item">
                            <input type="text" value={Tools.fnum(this.props.value)} readOnly class="form__input form__input_style_grow" />
                        </div>

                        {this.props.isManual ?
                        <div class="handbrake-group__info">
                            <div class="info-tooltip">
                                <div data-tooltip-content={`#${uniqueId}`} class="info-tooltip__trigger js-tooltip tooltipstered">
                                    <SvgIcon name="icon_info" class="info-tooltip__icon" />
                                </div>
                                <div id={uniqueId} class="tooltipster__content">
                                    <div class="info-tooltip__title">{this.props.comment?.manager}</div>
                                    <div class="info-tooltip__title">Было: {this.props.comment?.before}</div>
                                    <div class="info-tooltip__title">Стало: {this.props.value}</div>
                                    <div class="info-tooltip__title">Дата изменения: {this.props.comment?.date}</div>
                                    <div class="info-tooltip__title">Комментарий: {this.props.comment?.comment}</div>

                                    <div class="info-tooltip__control">
                                        <a href="#manual" {...this.getLinkAttributes()} class="link js-modal">Изменить</a>
                                    </div>
                                </div>

                            </div>
                        </div>
                        : null}
                    </div>
                    <div class="handbrake-group__control">
                        <a href="#manual" {...this.getLinkAttributes()} class={"handbrake-group__button js-modal" + (this.props.isManual ? " handbrake-group__button_state_active" : "")}>
                            <SvgIcon name="icon_hand" class="handbrake-group__icon" />
                        </a>
                    </div>
                </div>
            </div>
        </div>
    }

    getLinkAttributes(){
        return {
            "data-modal_key": this.props.manualKey,
            "data-order_id": this.props.order_id,
            "data-bind_id": this.props.id,
            "data-value": this.props.value,
        }
    }
}