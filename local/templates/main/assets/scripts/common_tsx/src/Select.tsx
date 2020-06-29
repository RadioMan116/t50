import { Component, h } from "preact"

export type SelectItem = {val: number|string, title: string}
export type SelectItemCallback = (SelectItem) => void

interface Props {
    items: SelectItem[],
    onSelect: SelectItemCallback,
    value?: number|string,
    class?: string,
    default?: SelectItem & {disabled?: boolean}
}

export default class Select extends Component<Props, any>
{
    state: {value?:number|string}
    private ref: HTMLSelectElement

    private el: any

    constructor(props:Props) {
        super(props);
        this.state = {value: props.value};
    }

    private static KEY_COUNTER = 0;
    static key(){
        return "select_key_" + ++ Select.KEY_COUNTER;
    }

    componentWillReceiveProps(props:Props){
        this.setState({value: props.value})
    }

    componentDidMount(){
        this.el = $(this.ref);
        this.ref.onchange = this.select.bind(this)
    }

    componentWillUnmount() {
        ($("select.select2-hidden-accessible") as any).select2('destroy');
    }

    select(event: Event){
        let target = event.target as HTMLSelectElement;
        let selectedItem = this.getItems().find(item => {
            return item.val == target.value;
        });

        if( selectedItem == null )
            return

        this.setState({value: selectedItem.val})
        if( this.props.onSelect != null )
            this.props.onSelect(selectedItem);
    }

    getItems(){
        let items = [...this.props.items];
        let firstItem = this.props.default;
        if( firstItem != null ){
            firstItem.disabled = firstItem.disabled ?? true;
            items.unshift(firstItem);
        }

        return items;
    }

    getOptions(){
        return this.getItems().map(item => {
            let selected = item.val == this.state.value;
            let disabled = item["disabled"] ?? false;
            return <option disabled={disabled} value={item.val} selected={selected}>{item.title}</option>
        })
    }

    render() {
        let disabled = ( this.props.items.length == 0 );
        return <select disabled={disabled} ref={ref => this.ref = ref} class={this.props.class} value={this.state.value}>
            {this.getOptions()}
        </select>
    }
}