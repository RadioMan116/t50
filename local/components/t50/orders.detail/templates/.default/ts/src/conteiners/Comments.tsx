import { Component, h } from "preact";
import { IRootReducer } from "@Root/reducers";
import { connect } from "preact-redux";
import SvgIcon from "@Components/SvgIcon"
import BaseComponent from "./BaseComponent";
import Checkbox from "@Components/Checkbox";
import CommentsAction, { DataCommentModal } from "@Root/actions/CommentsAction";
import { IComments, ICommentsFilter } from "@Root/reducers/comments";
import { OrderId, Manager, CommentItem } from "@Root/types";
import DateTimePicker from "@Root/components/DateTimePicker";
import Select, { SelectItem } from "@Root/components/Select";
import CommonTools from "@Root/tools/CommonTools";
import Comparator from "@Root/tools/Comparator";


class Comments extends BaseComponent<IMapStateToProps>
{
    componentWillMount(){
        T50PubSub.subscribe("comment_modal_submit", async (commentData) => {
            let success = await CommentsAction.addComment(commentData)
            if( success )
                ($ as any).fancybox.close();
        })
        CommentsAction.loadByFilter(this.props.filter)
    }

    changeFilter(name: keyof ICommentsFilter, e: Event){
        let target = e.target as HTMLInputElement
        CommentsAction.changeFilter(name, target.value)
    }

    changeSelect(name: keyof ICommentsFilter, val: SelectItem){
        CommentsAction.changeFilter(name, val.val)
    }

    submitFilter(){
        CommentsAction.loadByFilter(this.props.filter)
    }

    resetFilter(){
        CommentsAction.resetFilter()
    }

    unsetRemind(item: CommentItem, value: boolean){
        if( value == false && item.remind_date != null && confirm("Отключить напоминание?") )
            CommentsAction.changeItemRemindData(item.id, null)
    }

    updateRemindData(item: CommentItem, key: "remind_date" | "remind_time", event: Event){
        let value = (event.target as HTMLInputElement).value
        if( value.length == 0 )
            return
        let data = {remind_date: item.remind_date, remind_time: item.remind_time}
        data[key] = value

        if( data.remind_date == null )
            return

        CommentsAction.changeItemRemindData(item.id, data)
    }

    getComment(comment: CommentItem){
        const getTheme = (themeId: number) => {
            let theme = this.props.themes.find(theme => theme.val == themeId)
            return theme?.title
        }
        return [
            <div class="table__caption"><span class="table__marked">{comment.manager}</span>
            <span class="table__reduced table__rt-info">{comment.date}</span></div>
            ,
            <div class="table table_style_simple table_width_auto">
                <table class="table__main">
                    <tr class="table__tr">
                        <td class="table__td table__td_close_top">
                            <div class="form__field form__field_size-xl_m">
                                <input value={getTheme(comment.theme)} readOnly class="form__input" />
                            </div>
                        </td>
                        <td class="table__td table__td_close_top">
                            <div class="form__field form__field_size-xxxl_m">
                                <div class="form__input form__input_type_pseudo form__input_width_auto form__input_style_disabled">
                                    {comment.target_managers.length?
                                    <span class="form__mark">{comment.target_managers.map(manager => CommonTools.getManager(manager)).join(", ")}, </span>
                                    :null}
                                    {comment.message}
                                </div>
                            </div>
                        </td>

                        <td class="table__td table__td_close_top">
                            <div class="form__field">
                                <Checkbox checked={comment.remind} onClick={this.unsetRemind.bind(this, comment)} text="напомнить"/>
                            </div>
                        </td>
                        <td class="table__td table__td_close_top">
                            <div class="dual-panel">
                                <div class="dual-panel__row">
                                    <div class="dual-panel__col">
                                        <div class="form__field form__field_size-m_s">
                                            <DateTimePicker type="date" onChange={this.updateRemindData.bind(this, comment, "remind_date")} value={comment.remind_date}/>
                                        </div>
                                    </div>
                                    <div class="dual-panel__col">
                                        <div class="form__field form__field_size-s_s">
                                            <DateTimePicker type="time" onChange={this.updateRemindData.bind(this, comment, "remind_time")} value={comment.remind_time}/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        ]
    }

    componentDidUpdate(){
        this.jqueryUpdate = false
        super.componentDidUpdate()
    }

    render() {
        return <div>

            <div class="form__section form__section_close_top">

                <div class="table table_style_simple table_width_auto">
                    <table class="table__main">
                        <tr class="table__tr">
                            <td class="table__td">
                                <div class="dual-panel">
                                    <div class="dual-panel__row">
                                        <div class="dual-panel__col">
                                            <label class="form__line form__line_type_close form__field form__field_size-m_s">
                                                <div class="form__label">Дата</div>
                                                <DateTimePicker type="date" onChange={this.changeFilter.bind(this, "date_from")} value={this.props.filter.date_from}/>
                                            </label>
                                        </div>
                                        <div class="dual-panel__col">
                                            <label class="form__line form__line_type_close form__field form__field_size-m_s">
                                                <div class="form__label">&nbsp;</div>
                                                <DateTimePicker type="date"  onChange={this.changeFilter.bind(this, "date_to")} value={this.props.filter.date_to}/>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="table__td">
                                <label class="form__line form__line_type_close form__field form__field_size-xl_m">
                                    <div class="form__label">Менеджер</div>
                                    <Select key={Select.key()} items={CommonTools.getManagersForSelection()} default={{title: "-", val: ""}}
                                        class="js-select form__select" onSelect={this.changeSelect.bind(this, "manager")} value={this.props.filter.manager} />
                                </label>
                            </td>
                            <td class="table__td">
                                <label class="form__line form__line_type_close form__field form__field_size-xl_m">
                                    <div class="form__label">Тема</div>
                                    <Select key={Select.key()} items={this.props.themes} class="js-select form__select" default={{title: "-", val: ""}}
                                            onSelect={this.changeSelect.bind(this, "theme")} value={this.props.filter.theme} />
                                </label>
                            </td>
                            <td class="table__td">
                                <div class="form__label">&nbsp;</div>
                                <button class="button button_style_dark button_type_concrete" type="button" onClick={this.submitFilter.bind(this)}>Применить фильтр</button>
                            </td>
                            <td class="table__td">
                                <div class="form__label">&nbsp;</div>
                                <button class="button button_style_default button_type_concrete" type="button" onClick={this.resetFilter.bind(this)}>Сбросить</button>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            {this.props.items.map(item => this.getComment(item))}

            <div class="panel__foot panel__foot_type_full">
                <div class="panel__main">
                    <a class="button button_type_concrete js-modal" href="#order_comment">Добавить комментарий</a>
                </div>
            </div>

        </div>
    }
}



type IMapStateToProps = IComments

const mapStateToProps = (state: IRootReducer): IMapStateToProps => {
    return state.comments
}
export default connect(mapStateToProps)(Comments)