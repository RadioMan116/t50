import { Component, h, createRef } from "preact";
import { store } from "../Stor";
import { changeValue } from "../actions";

interface Props
{
    value: number
}
export default class InputAvail extends Component<Props>
{
    private select: HTMLSelectElement

    componentDidMount(){
        $(this.select).on('change', () => {
            let val = parseInt($(this.select).val().toString())
            store.dispatch(changeValue(val))
        })
    }

    componentWillUnmount(){
        $(this.select).removeClass("select2-hidden-accessible")
        $(this.select).unbind()
    }

    componentDidUpdate(){
        window["common"].jsSelectInit()
    }

    render(){
        return <label class="form__line" >
            <label class="form__label">Статус наличия</label>
            <select name="value" class="js-select modal__select"  ref={ref => this.select = ref} value={this.props.value}>
                <option value={1} selected={this.props.value == 1}>В наличии</option>
                <option value={2} selected={this.props.value == 2}>Под заказ</option>
                <option value={3} selected={this.props.value == 3}>Нет в наличии</option>
            </select>
        </label>
    }
}