import { Component, h } from "preact";
import { IRootReducer } from "@Root/reducers";
import { connect } from "preact-redux";
import BaseComponent from "./BaseComponent";
import Checkbox from "@Components/Checkbox";
import BasketTools from "@Root/tools/BasketTools";
import CommonAction from "@Root/actions/CommonAction";

class Header extends BaseComponent<IMapStateToProps> {

    switchTestFlag(value: boolean){
        CommonAction.setValue("is_test", (value ? 1 : 0))
    }

    render() {
        const {orderId, is_test} = this.props
        return <div class="panel__head">
            <div class="panel__head-main">
                <h2 class="panel__title">Заказ #{orderId}
                    <span class="panel__head-minor">{ this.props.has_claim ?"(в рекламации)" : "" }</span>
                    <span class="panel__head-check">
                        <Checkbox checked={is_test} text="Тестовый" onClick={this.switchTestFlag.bind(this)}/>
                    </span>
                </h2>
            </div>
            <div class="panel__head-controls">
                <a class="link link_style_marked js-modal" data-type="inline" href="#order_view">Редактировать вид</a>
            </div>
        </div>

    }
}


interface IMapStateToProps {
    orderId: number,
    is_test: boolean,
    has_claim: boolean,
}

const mapStateToProps = (state: IRootReducer): IMapStateToProps => ({
    orderId: state.order.order_id,
    is_test: state.order.is_test,
    has_claim: BasketTools.hasClaim()
});

export default connect(mapStateToProps)(Header)