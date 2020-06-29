import { h, Component } from "preact";
type Props = {
    value: string,
    onChange?: (value: string) => void
}
export default class PhoneInput extends Component<Props>
{
    state: {value: string} = {value: ""}

    constructor(props: Props){
        super(props)
        this.propsToState(props)
    }

    componentWillReceiveProps(props: Props){
        this.propsToState(props)
    }

    propsToState(props: Props){
        this.setState({value: props.value})
    }

    onInput(e: Event){
        let value = (e.target as HTMLInputElement).value
        value = value.replace(/[^+\d]/g, "");
        if( value.startsWith("+7") )
            value = "8" + value.substr(2)

        if( value.length > 11 )
            value = value.substr(0, 11);

        this.setState({value});
    }

    onChange(){
        if( this.props.onChange != null )
            this.props.onChange(this.state.value)
    }

    render() {
        return <input value={this.state.value} onInput={this.onInput.bind(this)} onChange={this.onChange.bind(this)} class="form__input" />
    }
}