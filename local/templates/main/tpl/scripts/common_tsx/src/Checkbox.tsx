import { h, Component } from "preact";
type Props = {
    text?: string,
    checked?: boolean,
    onClick?: (checked: boolean) => boolean | any,
    awaitConfirm?: true,
}
export default class Checkbox extends Component<Props>
{
    private static checkboxCounter = 0;
    state: {checked: boolean} = {checked: false}

    constructor(props: Props){
        super(props)
        this.propsToState(props)
    }

    componentWillReceiveProps(props: Props){
        this.propsToState(props)
    }

    propsToState(props: Props){
        let checked = (props.checked == null ? false : props.checked);
        this.setState({checked})
    }

    onChange(){
        let newState = !this.state.checked;
        let canChaneState = true;
        if( this.props.onClick != null ){
            let result = this.props.onClick(newState);
            if( this.props.awaitConfirm && result !== true ){
                canChaneState = false
            }
        }
        this.setState({checked: ( canChaneState ? newState : this.state.checked)})
    }

    render() {
        let id = `checkbox_${++Checkbox.checkboxCounter}`

        return <div class="check-elem">
            <input type="checkbox" id={id} class="check-elem__input" checked={this.state.checked} onChange={this.onChange.bind(this)} />
            <label for={id} class="check-elem__label">{this.props.text}</label>
        </div>
    }
}