import { h, Component } from "preact";
import { store } from "../Stor";
import { changeValue } from "../actions";
import { ModalKey } from "../types_consts";

type Props = {modal_key: ModalKey, value: number}
export default class InputPrice extends Component<Props>
{
    state: {value: string} = {value: ""}
    onChange(e: Event){
        let target = e.target as HTMLInputElement
        let num = parseFloat(target.value.replace(",", "."))
        if( isNaN(num) || num <= 0 ){
            this.forceUpdate()
        } else {
            this.setState({value: num})
            store.dispatch(changeValue(num))
        }
    }

    componentDidMount(){
        this.setState({value: this.props.value})
    }

    render() {
        let title = (this.props.modal_key.indexOf("sale") != -1 ? "продажи" : "закупки")
        return <label class="form__line">
            <label class="form__label">{`Цена ${title}, руб`}</label>
            <input value={this.state.value} autocomplete="off" class="form__input" type="text" onInput={this.onChange.bind(this)} />
        </label>
    }

}