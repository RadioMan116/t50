import { h, Component, render } from "preact";
import SvgIcon from "./SvgIcon";

type Props = {
    type: "time" | "date",
    value?: string,
    onChange?: (e) => void
    showIcon?: boolean
}
export default class DateTimePicker extends Component<Props>
{
    state: { value?: string } = { value: "" }

    private inputDate: HTMLInputElement
    private inpuTime: HTMLInputElement

    componentDidMount() {
        this.setState({ value: this.props.value })

        if (this.props.type == "time") {
            $(this.inpuTime).change(event => {
                this.onChange(event);
            })
            return
        }

        $(this.inputDate)["datetimepicker"]({
            timepicker: false,
            format: 'd.m.Y',
            scrollMonth: false,
            scrollInput: false,
        }).on('change', event => {
            this.onChange(event);
        })

    }

    componentWillReceiveProps(props: Props) {
        if (this.state.value != props.value)
            this.setState({ value: props.value })
    }

    onChange(event: JQuery.ChangeEvent<HTMLInputElement, null, HTMLInputElement, HTMLInputElement>) {
        if (this.state.value == event.target.value)
            return

        this.setState({ value: event.target.value })
        if (this.props.onChange != null)
            this.props.onChange(event)
    }

    renderDate() {
        return <label class={this.props.showIcon ?? true ? "form__input-wrapper" : ""}>
            <input type="text" value={this.state.value} class="form__input" ref={ref => this.inputDate = ref} />
            {this.props.showIcon ?
                <SvgIcon name="icon_calendar" class="form__input-icon" />
                : null}
        </label>
    }

    renderTime(){
        return <input value={this.state.value} class="form__input js-time" ref={ref => this.inpuTime = ref} />
    }

    render() {
        return ( this.props.type == "date" ? this.renderDate() : this.renderTime() )
    }
}