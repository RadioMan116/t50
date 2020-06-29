import { Component, h } from "preact";
import Checkbox from "./Components/Checkbox";
import SvgIcon from "./SvgIcon";
import Select, { SelectItem } from "./Components/Select";
import DateTimePicker from "./Components/DateTimePicker";
import FormPersons, { FormPersonsData } from "./FormPersons";

type Manager = {id: number, name: string}

interface IState
{
    remind: boolean,
    remind_date: string,
    remind_time: string,
    themes: SelectItem[],
    theme: number,
    forReclamation: boolean,
}

export default class App extends Component
{
    private searchInput: HTMLInputElement
    state:IState = {
        remind: false,
        remind_date: null,
        remind_time: null,
        themes: window["themes"],
        theme: null,
        forReclamation: false,
    }
    private formPersonsData:FormPersonsData = {
        comment: "",
        targetManagers: [],
    }

    componentDidMount(){
        $(".modal[id='order_comment']").on( "modal_loaded", (event, opts) => {
            let data = opts.$orig.data();
            this.setState({forReclamation: ( data.reclamation == "Y" )})
        });
    }

    componentDidUpdate(){
        var $select = $(this.base).find('.js-select:visible') as any;
		$select.select2({
			minimumResultsForSearch: -1
		});
    }

    setDateTime(code: "remind_date" | "remind_time", event: Event){
        let value = (event.target as HTMLInputElement).value
        if( code =="remind_date" )
            this.setState({remind_date: value})

        if( code =="remind_time" )
            this.setState({remind_time: value})
    }

    setRemind(value: boolean){
        this.setState({remind: value})
    }

    setTheme(selItem: SelectItem){
        this.setState({theme: selItem.val})
    }

    submit(){
        let submitData = {... this.state, ...this.formPersonsData}
        delete submitData.themes
        if( submitData.forReclamation ){
            T50PubSub.send("reclamation_comment_modal_submit", submitData)
        } else {
            T50PubSub.send("comment_modal_submit", submitData)
        }
    }

    render() {
        return <form class="form">
            {this.state.forReclamation ? null :
                <label class="form__line">
                    <div class="form__label">Тема</div>
                    <Select items={this.state.themes} value={this.state.theme} onSelect={this.setTheme.bind(this)} class="js-select modal__select" />
                </label>
            }
            <div class="form__line">
                <div class="triple-group">
                    <div class="triple-group__item">
                        <Checkbox checked={this.state.remind} text="напомнить" onClick={this.setRemind.bind(this)} />
                    </div>
                    <div class="triple-group__item">
                        <div class="form__field form__field_size-m_s form__field_type_inline">
                            <DateTimePicker type="date" value={this.state.remind_date} onChange={this.setDateTime.bind(this, "remind_date")} showIcon={true} labelClass="form__input-wrapper"/>
                        </div>
                    </div>
                    <div class="triple-group__item">
                        <DateTimePicker type="time" value={this.state.remind_time} onChange={this.setDateTime.bind(this, "remind_time")} labelClass="form__field form__field_size-xs_s form__field_type_inline"/>
                    </div>
                </div>
            </div>
            <FormPersons onUpdate={data => this.formPersonsData = data}/>
            <div class="form__controls form__controls_align_center modal__controls">
                <div class="form__control modal__control">
                    <button class="button button_width_full" type="button" onClick={this.submit.bind(this)}>Добавить комментарий</button>
                </div>
            </div>
        </form>
    }
}