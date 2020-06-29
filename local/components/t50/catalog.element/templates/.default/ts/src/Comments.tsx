import { Component, h } from "preact";
import SvgIcon from "./SvgIcon";

type CommentType = "common" | "product" | "brand" | "shop"
type RadioButton = { code: CommentType }
type Comment = RadioButton & { text: string }
type State = {
    selectedType?: CommentType,
    radiobuttons: RadioButton[],
    comments: Comment[],
    collapsed: boolean,
}
export type CommentProps = {
    product_id: number,
    brand_id: number,
    shop_id: number,
    can_change: boolean,
}
export default class Comments extends Component<CommentProps> {

    private commentsTitle = {
        product: "О товаре",
        brand: "О бренде",
    }
    state: State = {
        radiobuttons: [
            { code: "product" },
            { code: "brand" },
        ],
        comments: [],
        collapsed: false
    }

    async componentWillMount() {
        let answer = await T50Ajax.postJson<{ comments: Comment[] }>("catalog.element", "comments_load", { product_id: this.props.product_id })
        if (answer.result)
            this.setState({ comments: answer.data.comments })
    }

    componentDidUpdate(){
        if( !this.props.can_change && this.state.comments && this.state.comments.length == 0 )
            $("#comments_block").hide();
    }

    selectType(type: CommentType) {
        this.setState({ selectedType: type })
    }

    collapse(e: Event) {
        e.preventDefault();
        this.setState({ collapsed: !this.state.collapsed })
    }

    async onSubmit(e: Event) {
        e.preventDefault()
        let data = {}
        $("#comment_form").serializeArray().forEach(input => {
            data[input.name] = input.value
        })
        let result = await this.updateComment(data)
        if( result )
            this.setState({ selectedType: null})
    }

    removeComment(type: CommentType, e: Event) {
        e.preventDefault()
        e.stopPropagation()
        if (!confirm("Удалить комментарий?"))
            return

        let data = {
            comment_type: type,
            comment: "clear",
            clear: true,
        }
        this.updateComment(data)
        return false
    }

    async updateComment(data: {}) {
        let id = this.props.product_id

        if (data["comment_type"] == "brand")
            id = this.props.brand_id

        if (data["comment_type"] == "shop")
            id = this.props.shop_id

        data["product_id"] = this.props.product_id
        data["id"] = id

        let answer = await T50Ajax.postJson<{ comments: Comment[] }>("catalog.element", "update_comment", data)
        if (!answer.result) {
            T50Notify.error("Ошибка")
            return false;
        }
        T50Notify.success("Обновлено")
        this.setState({ comments: answer.data.comments })
        return true;
    }

    render() {
        return <div class="panel">
            {this.state.comments.length > 0 ?
            <div class="panel__head">
                <div class="panel__head-main">
                    <h2 class="panel__title panel__title_type_subtitle">Комментарии&nbsp;<span class="panel__mark">{this.state.comments.length}</span></h2>
                </div>
                <div class="panel__head-controls">
                    <a class="link link_type_trigger link_style_trigger" href="#" onClick={this.collapse.bind(this)}>
                        {this.state.collapsed ? "Показать все комментарии" : "Свернуть все комментарии"}
                    </a>
                </div>
            </div>
            : null}
            <div class="comments-group">

                {this.getComments()}

                {this.props.can_change ?
                <form class="comments-group__form form" onSubmit={this.onSubmit.bind(this)} id="comment_form">

                    <div class="comments-group__subtitle">Добавить комментарий</div>
                    <div class="comments-group__properties">
                        <ul class="properties-group">
                            {this.getRadioButtons()}
                        </ul>
                    </div>

                    {this.getComment()}

                    <div class="form__controls">
                        <div class="form__control">
                            <button class="button button_style_uppercase" type="submit">Отправить</button>
                        </div>
                        <div class="form__control">
                            <button class="button button_style_default button_style_uppercase" type="reset">Отменить</button>
                        </div>
                    </div>

                </form>
                : null}
            </div>
        </div >
    }

    getComment() {
        let current = this.state.comments.find(comment => {
            return comment.code == this.state.selectedType
        })
        let comment = (current == null ? "" : current.text)


        return <textarea name="comment" disabled={this.state.selectedType == null || !this.props.can_change} class="form__textarea" value={comment}></textarea>
    }

    getComments() {
        let pattern = /(\r\n|\r|\n)/g
        const prepareComment = (comment: string) => {
            return comment.split(pattern).map(line => ( pattern.test(line) ? <br/> : line ))
        }
        return this.state.comments.map(item => (
            <div class="comments-group__list">
                <div class="comments-list">
                    <div class="comments-list__title">
                        {this.commentsTitle[item.code]}
                        <span class="comments-list__info" onClick={this.selectType.bind(this, item.code)}>Кликни на комментарий для редактирования</span>
                    </div>
                    <div class={`comments-list__group ${this.state.collapsed ? "comments-list__group_state_collapse" : ""}`}>
                        <div class="comments-list__item" onClick={this.selectType.bind(this, item.code)}>
                            <div class="comments-list__wrapper comments-list__wrapper_type_editable">
                                {prepareComment(item.text)}

                                {this.props.can_change ?
                                    <a href="#" class="comments-list__remove" onClick={this.removeComment.bind(this, item.code)}>
                                        <SvgIcon name="icon_delete" class="comments-list__remove-icon" />
                                    </a>
                                    : null}
                            </div>
                        </div>
                    </div>
                </div>
            </div >
        ))
    }

    getRadioButtons() {
        return this.state.radiobuttons.map(item => (
            <li class="properties-group__item">
                <div class="check-elem check-elem_size_middle">
                    <input name="comment_type" value={item.code} type="radio" onClick={this.selectType.bind(this, item.code)} class="check-elem__input" id={`comment_${item.code}`} checked={item.code == this.state.selectedType} />
                    <label class="check-elem__label" for={`comment_${item.code}`}>{this.commentsTitle[item.code]}</label>
                </div>
            </li>
        ))
    }

}