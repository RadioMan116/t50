import { Component, h } from "preact"

interface IState
{
    product_id: number,
    analog_id: number,
    comment: string
}
export default class Modal extends Component
{
    private select: HTMLSelectElement;
    state: IState = {
        product_id: 0,
        analog_id: 0,
        comment: "",
    }

    constructor(props){
        super(props)
        $(".modal[id='analogs']").on( "modal_loaded", (event, opts) => {
            this.setState(opts.$orig.data());
        });
        $(".modal[id='manual']").on( "modal_close", (event, opts) => {
            this.setState({product_id: 0, analog_id: 0, comment: ""})
        });
    }

    componentDidMount() {
        $(this.select)["select2"]({
            tags: true,
            ajax: {
                url: "/ajax/catalog.element/analogs/",
                type: "post",
                dataType: "json",
                headers: { "X-CSRF-TOKEN": $("meta[name='sessid']").attr("content") },
                data: function (params) {
                    return {search: params.term};
                },
                delay: 500,
                processResults: function (answer: {data: []}) {
                    return {results: answer.data};
                }
            }
        }).on("select2:select", (event: Event) => {
            let value = parseInt((event.target as HTMLSelectElement).value);
            if( value > 0 )
                this.setState({analog_id: value});
        });
    }

    async submit(){
        let postData = {product_id: this.state.product_id, comment: this.state.comment, analog_id: this.state.analog_id};
        T50PubSub.send("set_analog_submit", postData)
    }

    changeComment(event: Event){
        let value = (event.target as HTMLTextAreaElement).value;
        this.setState({comment: value});
    }

    render() {
        return <div >
            <h3 class="modal__title modal__title_align_center">Добавление аналогов</h3>
            <div class="modal__content">
                <form class="form">
                    <label class="form__line">
                        <label class="form__label">Введите аналог</label>
                        <select class="modal__select" ref={ref => this.select = ref } />
                    </label>

                    <label class="form__line">
                        <span class="form__label">Обоснование</span>
                        <textarea class="form__textarea" value={this.state.comment} onInput={this.changeComment.bind(this)}></textarea>
                    </label>

                    <div class="form__controls form__controls_align_center modal__controls">
                        <div class="form__control modal__control">
                            <button class="button button_width_full" type="button" onClick={this.submit.bind(this)}>Добавить</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    }

}