import { Component, h } from "preact";
import { FORMULA_PARAMS_BLOCK } from "@Root/types";
import { IRootReducer } from "@Root/reducers";
import { connect } from "preact-redux";
import Checkbox from "@Components/Checkbox";
import SvgIcon from "@Components/SvgIcon";
import { changeCheckRRC, changeParameters } from "@Actions/parametersActions";
import { store } from "@Root/Stor";

class Parameters extends Component<IMapStateToProps>
{


    componentDidMount(){
        this.setState({blocks: this.props.blocks, check_rrc: this.props.check_rrc})
    }

    addEmpty(){
        let blocks = this.props.blocks;
        blocks.push({})
        store.dispatch(changeParameters(blocks))
    }

    removeRow(index: number){
        let blocks = this.props.blocks;
        blocks.splice(index, 1)
        store.dispatch(changeParameters(blocks))
    }

    getInput(code: keyof FORMULA_PARAMS_BLOCK, paramsBlock: FORMULA_PARAMS_BLOCK, index: number){
        return <input value={paramsBlock[code]} class="form__input" onInput={(e: Event) => {
            let blocks = this.props.blocks
            let value = (e.target as any).value as number;
            let strValue = value.toString()
            if( code == "percent"){
                value = parseFloat(strValue)
            } else {
               value = parseInt(strValue)
            }
            if( isNaN(value) )
                value = null
            blocks[index][code] = value
            store.dispatch(changeParameters(blocks))
        }}/>
    }

    getRow(paramsBlock: FORMULA_PARAMS_BLOCK, index: number) {
        let isFirst = (index == 0);
        return <tr class="table__tr">
            <td class="table__td table__td_type_close table__td_size_auto">
                <label class="form__line form__line_type_close form__field form__field_size-s_l">
                    <div class="form__label">Мин. комиссия</div>
                    {this.getInput("min_commission", paramsBlock, index)}
                </label>
            </td>
            <td class="table__td table__td_type_close table__td_size_auto">
                <label class="form__line form__line_type_close form__field form__field_size-xl_xs">
                    <div class="form__label">Закуп. цена</div>
                    <div>
                        <div class="range__fields">
                            <div class="range__field">{this.getInput("min_purchase", paramsBlock, index)}</div>
                            <div class="range__field">{this.getInput("max_purchase", paramsBlock, index)}</div>
                        </div>
                        <div class="range__slider"></div>
                    </div>
                </label>
            </td>
            <td class="table__td table__td_type_close table__td_size_auto">
                <label class="form__line form__line_type_close form__field form__field_size-s_l">
                    <div class="form__label">Вход+%</div>{this.getInput("percent", paramsBlock, index)}
                </label>
            </td>
            <td class="table__td table__td_type_close table__td_size_auto">
                <label class="form__line form__line_type_close form__field form__field_size-s_l">
                    <div class="form__label">Макс. комиссия</div>{this.getInput("max_commission", paramsBlock, index)}
                </label>
            </td>
            <td class="table__td table__td_type_close table__td_size_auto">
                <div class="table__td-wrapper">
                    <div class="form__label">&nbsp;</div>
                    <div class={"form__field " + (isFirst ? "" : "order__top-connect")}>
                        <div class="table__td-disabled">
                            <Checkbox checked={this.props.check_rrc} text="Не ниже РРЦ" onClick={changeCheckRRC} />
                        </div>
                    </div>
                </div>
            </td>
            <td class="table__td table__td_type_close table__td_size_auto table__td_align_right">
                <div class="form__label">&nbsp;</div>

                {   isFirst ?
                    <span class="link link_type_wrapper" onClick={this.addEmpty.bind(this)}>
                        <SvgIcon class="table__add" name="icon_add" />
                    </span>
                    :
                    <span class="link link_icon_big" onClick={this.removeRow.bind(this, index)}>
                        <SvgIcon class="link__icon" name="icon_delete" />&nbsp;
                    </span>
                }

            </td>
        </tr>
    }

    getRows() {
        return this.props.blocks.map((data, i) => {
            return this.getRow(data, i);
        });
    }

    render() {
        return <div class="table table_style_simple table_width_auto">
            <table class="table__main">
                {this.getRows()}
            </table>
        </div>
    }
}


interface IMapStateToProps {
    check_rrc: boolean,
    blocks: FORMULA_PARAMS_BLOCK[],
}

const mapStateToProps = (state: IRootReducer): IMapStateToProps => ({
    check_rrc: state.parameters.check_rrc,
    blocks: state.parameters.blocks,
});

export default connect(mapStateToProps)(Parameters)