import { Component, h } from "preact";
import RadioButtons from "./RadioButtons";

type OriginData = {
    email_type?: string,
    order_id?: number,
}

interface IState {
    email_type: string,
    show_additions: boolean,
    content_type?: string,
    from?: string,
    to?: string,
    subject?: string,
    body?: string,
    file?: string,
    priority?: string,
    reply_to?: string,
    cc?: string,
    bcc?: string,
    history: string[],
}

export default class App extends Component {

    state: IState = { show_additions: false, email_type: "", history: [] }
    private originData: OriginData;

    private templates = [
        { value: "order_for_supplier", title: "Данные по заказу" },
        { value: "request_avail", title: "Запрос наличия у поставщика" },
    ]

    componentWillMount() {
        $(".modal[id='email_editor']").on("modal_loaded", async (event, opts) => {
            let data = opts.$orig.context.dataset;
            this.originData = {...data as OriginData};
            this.loadTemplate(this.originData.email_type);
        });
    }

    onInput(code: keyof IState, e: Event) {
        let value = (e.target as HTMLInputElement).value
        let obj = {}
        obj[code] = value

        this.setState(obj)
    }

    openAdditionals() {
        this.setState({ show_additions: true })
    }

    async loadTemplate(email_type: string){
        this.setState({email_type: email_type});
        if( !email_type )
            return;

        if( email_type == "*" ){
            let answer = await T50Ajax.postJson<IState>("orders.detail", "mailing_load_history", this.originData)
            if (answer.result && Array.isArray(answer.data) && answer.data.length > 0)
                this.setState({history: answer.data});
            return;
        }

        this.originData.email_type = email_type;

        let answer = await T50Ajax.postJson<IState>("orders.detail", "mailing_load_data", this.originData)
        if (answer.result) {
            this.setState({...answer.data, ...this.originData});
        }
    }

    async submit() {
        let answer = await T50Ajax.postJson<IState>("orders.detail", "mailing_send", this.state)
        if (answer.result) {
            ($ as any).fancybox.close();
            T50PubSub.send("mailing_send", this.state.email_type);
            T50Notify.success("Письмо отправлено");
        } else {
            T50Notify.error("Ошибка");
        }
    }

    selectTypes() {
        const selectType = (value: string) => {
            this.loadTemplate(value)
        }

        return <div class="modal__content">
            <RadioButtons data={this.templates} onChange={selectType} name="templates"  itemClass="table__item" value=""/>

            <br/>

            {this.state.email_type != "*" ? null:

            <div class="table__item">
                <ol>
                    {this.state.history.map(history => <li>{history}</li>)}
                </ol>
            </div>

            }
        </div>
    }

    render() {
        if ( !this.state.email_type )
            return;

        if ( this.state.email_type == "*" )
            return this.selectTypes();

        const additionalClass = (isAdditional: boolean) => {
            return this.state.show_additions != isAdditional ? " hide " : ""
        }
        return <div class="modal__content">
            <form class="form" >
                <label class="form__line">
                    <span class="form__label">Кому</span>
                    <input class="form__input" onInput={this.onInput.bind(this, "to")} value={this.state.to} />
                </label>

                <label class="form__line">
                    <span class="form__label">Тема</span>
                    <input class="form__input" onInput={this.onInput.bind(this, "subject")} value={this.state.subject} />
                </label>


                <label class={"form__line" + additionalClass(true)}>
                    <span class="form__label">Копия</span>
                    <input class="form__input" onInput={this.onInput.bind(this, "cc")} value={this.state.cc} />
                </label>

                <label class={"form__line" + additionalClass(true)}>
                    <span class="form__label">Скрытая копия</span>
                    <input class="form__input" onInput={this.onInput.bind(this, "bcc")} value={this.state.bcc} />
                </label>

                <label class={"form__line" + additionalClass(true)}>
                    <span class="form__label">Обратный адрес</span>
                    <input class="form__input" onInput={this.onInput.bind(this, "reply_to")} value={this.state.reply_to} />
                </label>


                <label class={"form__line" + additionalClass(false)}>
                    <div class="check-elem">
                        <input type="radio" id="mailing" class="check-elem__input" onClick={this.openAdditionals.bind(this)} />
                        <label for="mailing" class="check-elem__label">Дополнительные поля</label>
                    </div>
                </label>


                <label class="form__line">
                    <textarea class="form__textarea" style="height: 350px" onInput={this.onInput.bind(this, "body")} value={this.state.body}></textarea>
                </label>

                <div class="form__controls form__controls_align_center modal__controls">
                    <div class="form__control modal__control">
                        <button class="button button_width_full" type="button" onClick={this.submit.bind(this)}>Отправить</button>
                    </div>
                </div>
            </form>
        </div>
    }
}