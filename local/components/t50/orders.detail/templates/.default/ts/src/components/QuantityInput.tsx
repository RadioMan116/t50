import { h, Component } from "preact";
type Props = {
    value: number,
    min?: number,
    max?: number,
    onChange?: (value: number) => void
}
export default class QuantityInput extends Component<Props>
{
    state: {value: number} = {value: 0}

    constructor(props: Props){
        super(props)
        this.propsToState(props)
    }

    componentWillReceiveProps(props: Props){
        this.propsToState(props)
    }

    propsToState(props: Props){
        let value = (props.value === null ? false : props.value);
        if( typeof value === "string" )
            value = parseInt(value)
        this.setState({value})
    }

    onChangeManual(e: Event){
        let input = e.target as HTMLInputElement
        if( !this.setValue(parseInt(input.value)) )
            this.setState({value: this.state.value})
    }

    onChange(add: number){
        let value = this.state.value;
        value += add
        this.setValue(value)
    }

    setValue(value: number){
        if( isNaN(value) )
            return false

        let min = ( this.props.min == null ? 0 : this.props.min )

        if( value < min )
            return false

        if( this.props.max != null && value > this.props.max )
            return false

        this.setState({value})
        if( this.props.onChange != null )
            this.props.onChange(value)

        return true
    }

    listenerArrows(e: KeyboardEvent){
        let add = 0;
        switch(e.code){
            case "ArrowUp":
                add = 1;
                break;
            case "ArrowDown":
                add = -1;
                break;
        }
        if( add == 0 )
            return

        e.preventDefault()
        this.onChange(add)
    }

    render() {
        return <div class="quantity-widget" >
            <input type="text" value={this.state.value} class="quantity-widget__input" onKeyDown={this.listenerArrows.bind(this)} onInput={this.onChangeManual.bind(this)} />
            <button type="button" class="quantity-widget__minus" onClick={this.onChange.bind(this, -1)}>-</button>
            <button type="button" class="quantity-widget__plus" onClick={this.onChange.bind(this, 1)}>+</button>
        </div>
    }
}