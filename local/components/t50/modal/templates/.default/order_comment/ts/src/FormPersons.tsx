import { Component, h } from "preact";
import SvgIcon from "./SvgIcon";

type Manager = { id: number, name: string }
type StateTypes = {
    open: boolean,
    search: string,
    comment: string,
    managers: Manager[],
    targetManagers: Manager[],
}
export type FormPersonsData = {
    comment: string,
    targetManagers: number[]
}
interface Props {
    onUpdate(obj: FormPersonsData): any
}
export default class FormPersons extends Component<Props> {
    state: StateTypes = { open: false, search: "", comment: "", managers: [], targetManagers: [] }
    private searchInput: HTMLInputElement
    private launchCallback = false

    componentDidMount() {
        $(document).mouseup((e: any) => {
            let $div = $(this.base)
            if (!$div.is(e.target) && $div.has(e.target).length === 0)
                this.setState({ open: false })
        });
    }

    componentDidUpdate() {
        if (this.launchCallback) {
            if (this.props.onUpdate != null) {
                this.props.onUpdate({
                    comment: this.state.comment,
                    targetManagers: this.state.targetManagers.map(namager => namager.id)
                })
            }
            this.launchCallback = false
        }
    }

    open() {
        this.setState({ open: true })
        if (this.searchInput != null) {
            setTimeout(() => {
                $(this.searchInput).focus()
            }, 200)
        }
    }

    async loadManagers(e: Event) {
        let value = (e.target as HTMLInputElement).value
        if (value.length < 2)
            return
        this.setState({ search: value })
        let answer = await T50Ajax.postJson<Manager>("common_ajax", "load_managers", { name: value })
        if (answer.result) {
            this.setState({ managers: answer.data ?? [] })
        }
    }

    selectManager(manager: Manager, e: Event) {
        e.preventDefault()
        let targetManagers = this.state.targetManagers
        let found = targetManagers.find(man => man.id == manager.id)
        if( !!found )
            return
        targetManagers.push(manager)
        this.setState({ targetManagers, open: false, search: "", managers: [] })
        this.launchCallback = true
    }

    removeManager(removeManager: Manager){
        let managers = this.state.targetManagers.filter(manager => {
            return manager.id != removeManager.id
        })
        this.setState({targetManagers: managers})
    }

    changeComment(e: Event) {
        let target = e.target as HTMLInputElement
        let value = target.value
        value = value.replace(target.dataset.managers, "")
        this.setState({ comment: value })
        this.launchCallback = true
    }

    getManager(manager: Manager) {
        let name = manager.name
        let reg = new RegExp(`(${this.state.search})`, "ig")
        name = name.replace(reg, `<span class="persons-finder__marked">\$1</span>`)
        return <li class="persons-finder__item">
            <a href="#" class="persons-finder__link" dangerouslySetInnerHTML={{ __html: name }} onClick={this.selectManager.bind(this, manager)}></a>
        </li>
    }

    getTextarea() {
        let targetManagers = this.state.targetManagers.map(manager => manager.name).join(", ");
        if (targetManagers.length)
            targetManagers += ", "

        let value = targetManagers + this.state.comment

        return <textarea class="form__textarea form__textarea_type_ninja form__textarea_size_normal" onInput={this.changeComment.bind(this)} data-managers={targetManagers} value={value}></textarea>
    }

    render() {
        return <label class="form__line">
            <div class="form__label">Комментарий</div>
            <div class="form__wrapper" >
                {this.getTextarea()}
                <div class="form__persons">
                    <div class={"persons-finder " + (this.state.open ? "persons-finder_state_open" : "")}>
                        <div class="persons-finder__trigger" onClick={this.open.bind(this)}>
                            <SvgIcon class="persons-finder__icon" name="icon_account" />
                        </div>
                        <div class="persons-finder__panel">
                            <div class="persons-finder__content">
                                <div class="persons-finder__form">
                                    <form class="form">
                                        <input type="text" value={this.state.search} class="form__input" onInput={this.loadManagers.bind(this)} ref={ref => this.searchInput = ref} />
                                    </form>
                                </div>
                                <div class="persons-finder__label">Люди</div>
                                <ul class="persons-finder__items">
                                    {this.state.managers.map(this.getManager.bind(this))}
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <ul class="order_comment_target_managers">
                {this.state.targetManagers.map(manager => (
                    <li>{manager.name} <a class="delete_link" onClick={this.removeManager.bind(this, manager)}></a></li>
                ))}
            </ul>

        </label>
    }
}