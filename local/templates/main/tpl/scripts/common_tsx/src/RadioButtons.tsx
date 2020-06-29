import { h, Component } from "preact";

type DataItem = {value: string, title: string}
interface Props {
    data: DataItem[],
    name: string,
    value: string,
    onChange?: (value: string) => void,
    itemClass: string
}

export default class RadioButtons extends Component<Props>
{
    state: {value?: string} = {value: null}
    static counter = 0

    constructor(props:Props){
        super(props)
        this.setState({value: this.props.value})
    }

    change(dataItem: DataItem){
        this.setState({value: dataItem.value});
        if( this.props.onChange != null )
            this.props.onChange(dataItem.value);
    }

    componentWillReceiveProps(props: Props){
        this.setState({value: props.value})
    }

    getItem(dataItem: DataItem) {
        let id = [this.props.name, (++RadioButtons.counter)].join("_")
        let checked = dataItem.value == this.state.value;

        return <div class={this.props.itemClass}>
            <div class="check-elem">
                <input type="radio" id={id} class="check-elem__input" checked={checked} onClick={this.change.bind(this, dataItem)} />
                <label for={id} class="check-elem__label">{dataItem.title}</label>
            </div>
        </div>
    }

    render() {
        return <div>
            {this.props.data.map(item => {
                return this.getItem(item)
            })}
        </div>
    }
}