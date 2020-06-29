import {  h, Component } from "preact";
import { store } from "../Stor";
import { changeDate } from "../actions";
import { ModalKey } from "../types_consts";

type Props = { value: string, modalKey?: ModalKey };
export default class InputDate extends Component<Props>
{
    private input: HTMLInputElement
    state: Props = { value: "", modalKey: null }

    componentDidMount(){
        $(this.input).on('change', (e: Event) => {
            this.input = e.target as HTMLInputElement;
            let date = $(this.input).val().toString()
            store.dispatch(changeDate(date))
        })
    }

    componentWillReceiveProps(props: Props){
    	this.setState({value: props.value})
    }

    render() {
        if( ["basket_sale", "basket_purchase", "install_sale", "install_purchase"].indexOf(this.props.modalKey) != -1 )
            return
        return <div class="grid-12__row">
            <div class="grid-12__col grid-12__col_size_7">
                <label class="form__line">
                    <span class="form__label">Конечная дата (необязательно)</span>
                    <input value={this.state.value} class="form__input js-date" type="text" autocomplete="off" ref={ref => this.input = ref}/>
                </label>
            </div>
        </div>
    }

}