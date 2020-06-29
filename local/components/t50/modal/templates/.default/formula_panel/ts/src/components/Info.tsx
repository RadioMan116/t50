import { Component, h } from "preact";
import { FORMULA } from "@Root/types";
import { store } from "@Root/Stor";
import { changeFormula } from "@Root/actions/formulasActions";

export default class Info extends Component<FORMULA>
{
    changeTitle(e: Event){
        let current = store.getState().formulas.current
        if( current == null )
            return
        let input = e.target as HTMLInputElement
        current.title = input.value
        store.dispatch(changeFormula(current))
    }

    render() {
        const {title, manager, date} = this.props
        if (title == null)
            return

        return <div class="grid-12__row">
            <div class="grid-12__col grid-12__col_size_6">
                <label class="form__line">
                    <span class="form__label">Итоговая формула</span>
                    <input value={title} class="form__input" onInput={this.changeTitle.bind(this)} type="text" />
                </label>
            </div>
            <div class="grid-12__col grid-12__col_size_4">
                <label class="form__line"><span class="form__label">Менеджер</span>
                    <input value={manager} disabled={true} class="form__input" type="text" />
                </label>
            </div>
            <div class="grid-12__col grid-12__col_size_2">
                <label class="form__line">
                    <span class="form__label">Дата отметки</span>
                    <input value={date} disabled={true} class="form__input" type="text" />
                </label>
            </div>
        </div>
    }
}