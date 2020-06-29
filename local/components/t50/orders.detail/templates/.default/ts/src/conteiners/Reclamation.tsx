import { Component, h } from "preact";
import { IRootReducer } from "@Root/reducers";
import { connect } from "preact-redux";
import SvgIcon from "@Components/SvgIcon"
import BaseComponent from "./BaseComponent";
import Checkbox from "@Components/Checkbox";
import ReclamationAction from "@Root/actions/ReclamationAction";
import { IReclamation, IReclamationScalar } from "@Root/reducers/reclamation";
import Select, { SelectItem } from "@Root/components/Select";
import { DocFile, CommentItem } from "@Root/types";
import CommonTools from "@Root/tools/CommonTools";
import DateTimePicker from "@Root/components/DateTimePicker";

class Reclamation extends BaseComponent<IMapStateToProps>
{
    private fileInput: HTMLInputElement

    componentDidMount() {
        ReclamationAction.loadAll()
        ReclamationAction.loadComments()
        $(this.fileInput).change(() => {
            let formData = new FormData()
            formData.append('file', $(this.fileInput)[0].files[0]);
            ReclamationAction.saveFile(formData)
            this.fileInput.value = ""
        })
        T50PubSub.subscribe("reclamation_comment_modal_submit", async (commentData) => {
            let success = await ReclamationAction.addComment(commentData)
            if (success)
                ($ as any).fancybox.close();
        })
    }

    unsetRemind(item: CommentItem, value: boolean) {
        if (value == false && item.remind_date != null && confirm("Отключить напоминание?"))
            ReclamationAction.changeCommentItemRemindData(item.id, null)
    }

    updateRemindData(item: CommentItem, key: "remind_date" | "remind_time", event: Event) {
        let value = (event.target as HTMLInputElement).value
        if (value.length == 0)
            return
        let data = { remind_date: item.remind_date, remind_time: item.remind_time }
        data[key] = value

        if (data.remind_date == null)
            return

        ReclamationAction.changeCommentItemRemindData(item.id, data)
    }


    onSelect(code: keyof IReclamationScalar, select: SelectItem) {
        ReclamationAction.update(code, select.val)
    }

    changeInput(code: keyof IReclamationScalar, e: Event) {
        let target = e.target as HTMLInputElement
        ReclamationAction.update(code, target.value)
    }

    saveFile(e: Event) {
        e.preventDefault()
        this.fileInput.name = name
        $(this.fileInput).trigger("click")
    }

    getFiles() {
        const deleteFile = (file: DocFile, e: Event) => {
            e.preventDefault();
            if( confirm("Удалить файл?") )
                ReclamationAction.removeFile(file.id)
        }
        return this.props.files.map(file => {
            return <li class="files-list__item">
                <a href={file.path} target="_blank" class="files-list__link">{file.title}</a>
                <a href="#" class="files-list__remove" onClick={deleteFile.bind(this, file)}>
                    <SvgIcon name="icon_delete" class="files-list__remove-icon" />
                </a>
            </li>
        })
    }

    getComments() {
        return this.props.comments.map(comment => {
            return [
                <div class="table__caption">
                    <span class="table__marked">{comment.manager}</span> <span class="table__reduced table__rt-info">{comment.date}</span>
                </div>
                ,
                <div class="table table_style_simple table_width_auto">
                    <table class="table__main">
                        <tr class="table__tr">
                            <td class="table__td table__td_close_top">
                                <div class="form__field form__field_size-xxxl_m">
                                    <div class="form__input form__input_type_pseudo form__input_width_auto form__input_style_disabled">
                                        {comment.target_managers.length ?
                                            <span class="form__mark">{comment.target_managers.map(manager => CommonTools.getManager(manager)).join(", ")}, </span>
                                            : null}
                                        {comment.message}
                                    </div>
                                </div>
                            </td>
                            <td class="table__td table__td_close_top">
                                <div class="form__field">
                                    <Checkbox checked={comment.remind} onClick={this.unsetRemind.bind(this, comment)} text="напомнить" />
                                </div>
                            </td>
                            <td class="table__td table__td_close_top">
                                <div class="dual-panel">
                                    <div class="dual-panel__row">
                                        <div class="dual-panel__col">
                                            <div class="form__field form__field_size-m_s">
                                                <DateTimePicker type="date" onChange={this.updateRemindData.bind(this, comment, "remind_date")} value={comment.remind_date} />
                                            </div>
                                        </div>
                                        <div class="dual-panel__col">
                                            <div class="form__field form__field_size-s_s">
                                                <DateTimePicker type="time" onChange={this.updateRemindData.bind(this, comment, "remind_time")} value={comment.remind_time} />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            ]
        })
    }

    render() {
        return <div>
            <div class="panel__section">
                <div class="form__line">
                    <div class="table table_style_simple table_width_auto">
                        <table class="table__main">
                            <tr class="table__tr">
                                <td class="table__td">
                                    <label class="form__line form__line_type_close form__field form__field_size-l_xs">
                                        <div class="form__label">Дата обращения</div>
                                        <DateTimePicker type="date" onChange={this.changeInput.bind(this, "date_request")} value={this.props.date_request} />
                                    </label>
                                </td>
                                <td class="table__td">
                                    <label class="form__line form__line_type_close form__field form__field_size-l_xs">
                                        <div class="form__label">Дата обработки</div>
                                        <DateTimePicker type="date" onChange={this.changeInput.bind(this, "date_start")} value={this.props.date_start} />
                                    </label>
                                </td>
                                <td class="table__td">
                                    <label class="form__line form__line_type_close form__field form__field_size-xl_xs">
                                        <div class="form__label">Причина обращения</div>
                                        <Select onSelect={this.onSelect.bind(this, "reason")} items={this.props.reasons} class="js-select form__select" value={this.props.reason}  default={{title: "-", val: 0, disabled: false}}/>
                                    </label>
                                </td>
                                <td class="table__td">
                                    <label class="form__line form__line_type_close form__field form__field_size-xl_xs">
                                        <div class="form__label">Требование клиента</div>
                                        <Select onSelect={this.onSelect.bind(this, "requirement")} items={this.props.requirements} class="js-select form__select" value={this.props.requirement}  default={{title: "-", val: 0, disabled: false}}/>
                                    </label>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="form__field form__field_size-xxxl_xs"><label class="form__line">
                    <div class="form__label">Описание</div>
                    <textarea class="form__textarea form__textarea_size_middle" onChange={this.changeInput.bind(this, "description")} value={this.props.description}></textarea>
                </label></div>
                <div class="form__section">
                    <div class="form__label">Прикрепленные документы</div>
                    <div class="files-field"><input type="file" class="hidden" />
                        <div class="files-field__list">
                            <ul class="files-list">
                                {this.getFiles()}
                            </ul>
                        </div>
                        <div class="files-field__controls">
                            <div class="files-field__control">
                                <input type="file" class="hidden" ref={ref => this.fileInput = ref} />
                                <a class="link link_style_default-trigger" href="#" onClick={this.saveFile.bind(this)}>Загрузить</a>
                            </div>
                        </div>
                    </div>
                </div>
                {this.props.comments?.length ?
                <div class="form__label">Хронология работы с рекламацией</div>
                : null}
                {this.getComments()}
                <div class="panel__foot panel__foot_type_full">
                    <div class="panel__main">
                        <a class="button button_type_concrete js-modal" data-reclamation="Y" href="#order_comment">Добавить комментарий</a>
                    </div>
                </div>
            </div>
            <div class="table table_style_simple table_width_auto">
                <table class="table__main">
                    <tr class="table__tr">
                        <th class="table__th">Дата решения</th>
                        <th class="table__th">Итог по рекламации</th>
                        <th class="table__th">Ошибка</th>
                        <th class="table__th">Ответственный менеджер</th>
                    </tr>
                    <tr class="table__tr">
                        <td class="table__td">
                            <label class="form__line form__line_type_close form__field form__field_size-l_xs">
                            <DateTimePicker type="date" onChange={this.changeInput.bind(this, "date_finish")} value={this.props.date_finish} />
                        </label>
                        </td>
                        <td class="table__td">
                            <label class="form__line form__line_type_close form__field form__field_size-xl_xs">
                                <Select onSelect={this.onSelect.bind(this, "result")} items={this.props.results} class="js-select form__select" value={this.props.result}  default={{title: "-", val: 0, disabled: false}}/>
                            </label>
                        </td>
                        <td class="table__td">
                            <label class="form__line form__line_type_close form__field form__field_size-xl_xs">
                                <Select onSelect={this.onSelect.bind(this, "error")} items={this.props.errors} class="js-select form__select" value={this.props.error}  default={{title: "-", val: 0, disabled: false}}/>
                            </label>
                        </td>
                        <td class="table__td">
                            <label class="form__line form__line_type_close form__field form__field_size-xl_xs">
                                <Select items={CommonTools.getManagersForSelection()} onSelect={this.onSelect.bind(this, "manager")} value={this.props.manager} class="js-select form__select" default={{title: "-", val: 0, disabled: false}}/>
                            </label>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    }
}



type IMapStateToProps = IReclamation

const mapStateToProps = (state: IRootReducer): IMapStateToProps => ({
    files: state.reclamation.files,
    description: state.reclamation.description,
    requirement: state.reclamation.requirement,
    requirements: state.reclamation.requirements,
    reason: state.reclamation.reason,
    reasons: state.reclamation.reasons,
    error: state.reclamation.error,
    errors: state.reclamation.errors,
    result: state.reclamation.result,
    results: state.reclamation.results,
    date_request: state.reclamation.date_request,
    date_finish: state.reclamation.date_finish,
    date_start: state.reclamation.date_start,
    manager: state.reclamation.manager,
    comments: state.reclamation.comments,
});

export default connect(mapStateToProps)(Reclamation)