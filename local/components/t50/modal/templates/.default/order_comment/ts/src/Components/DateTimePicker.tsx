import { h, Component, render } from "preact";
import SvgIcon from "./SvgIcon";

type Props = {
    type: "time" | "date",
    value?: string,
    onChange?: (e) => void
    showIcon?: boolean
    labelClass?: string
}
export default class DateTimePicker extends Component<Props>
{
    private inputDate: HTMLInputElement
    private inpuTime: HTMLInputElement

    componentDidMount() {
        if (this.props.type == "time") {
            $(this.inpuTime).change(event => {
                if (this.props.onChange != null)
                    this.props.onChange(event)
            })
            return
        }

        $(this.inputDate)["datetimepicker"]({
            timepicker: false,
            format: 'd.m.Y',
            scrollMonth: false,
            scrollInput: false,
        }).on('change', event => {
            if (this.props.onChange != null)
                this.props.onChange(event)
        })

    }

    render() {

        return this.props.type == "date" ?

            <label class={this.props.labelClass}>
                <input type="text" value={this.props.value} class="form__input" ref={ref => this.inputDate = ref} />
                {this.props.showIcon ?
                    <SvgIcon name="icon_calendar" class="form__input-icon" />
                    : null}
            </label>

            :

            <label class={this.props.labelClass}>
                <input value={this.props.value} class="form__input js-time" ref={ref => this.inpuTime = ref} />
            </label>
    }
}