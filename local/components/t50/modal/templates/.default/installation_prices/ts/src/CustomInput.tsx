import { Component, h } from "preact"
import { IRootReducer } from "./reducer"
import { connect } from "preact-redux"
import Action from "./actions"

class CustomInput extends Component<IRootReducer>
{
    onChange(e: Event){
        let value = (e.target as HTMLInputElement).value
        Action.setCustomInput(value);
    }

    shouldComponentUpdate(nextProps: IRootReducer){
        return ( this.props.custom_input != nextProps.custom_input );
    }

    render() {
        if( this.props.custom_input == null )
            return null

        let className = "form__input " + (this.props.custom_input.length == 0 ? "input_border_error" : "");
        return <label class="form__line">
            <span class="form__label">Название товара</span>
            <input type="text" class={className} value={this.props.custom_input} onInput={this.onChange.bind(this)} placeholder="Название товара" />
        </label>
    }
}

const mapStateToProps = (state: IRootReducer): IRootReducer => state;

export default connect(mapStateToProps)(CustomInput)