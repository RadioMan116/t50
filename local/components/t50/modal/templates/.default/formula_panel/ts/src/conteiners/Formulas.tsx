import { Component, h } from "preact";
import { FORMULA, FORMULA_SIMPLE, CITY } from "@Root/types";
import { IRootReducer } from "@Root/reducers";
import { connect } from "preact-redux";
import API from "@Root/API";
import { store } from "@Root/Stor";
import { resetFormula } from "@Root/actions/formulasActions";
import { addLog } from "@Root/actions/commonActions";

class Fromulas extends Component<IMapStateToProps>
{
    changeCurrentFormula(item: FORMULA){
        if( this.props.current != null && item.id == this.props.current.id )
            return

        API.loadFormula(item.id, this.props.city)
    }

    componentDidMount(){
        $(".modal[id='formula_panel']").on( "modal_loaded", (event, opts) => {
            if( opts.id > 0 ){
                API.loadFormula(opts.id, this.props.city)
            } else {
                store.dispatch(resetFormula(0))
            }
        });
    }

    async deleteFormula(item: FORMULA, event: MouseEvent){
        event.preventDefault();
        event.stopPropagation()

        if( !confirm(`Удалить формулу "${item.title}"?`) )
            return

        let answer = await API.deleteFormula(item.id)
        if( answer.result )
            addLog(`Удалена формула "${item.title}"`)

    }

    getSelect() {
        let options = this.props.items.map(item => <option value={item.id}>{item.title}</option>)
        if( this.props.current == null )
            options.unshift(<option value="0">"-"</option>)

        return <select class="pseudo-select__select" onChange={this.changeCurrentFormula.bind(this)}>{options}</select>
    }

    getDetails() {
        return this.props.items.map(item =>
            <span data-value={item.id} class="pseudo-select__item js-pseudo-select-item" onClick={this.changeCurrentFormula.bind(this, item)}>
                <div class="dual-panel">
                    <div class="dual-panel__row">
                        <div class="dual-panel__col"><span class="pseudo-select__text">{item.title}</span></div>
                        {item.canDelete ?
                        <div class="dual-panel__col dual-panel__col_align_right">
                            <a href="#" class="pseudo-select__link" onClick={this.deleteFormula.bind(this, item)}>Удалить</a>
                        </div>
                        : null}
                    </div>
                </div>
            </span>
        )
    }

    render() {
        return <span class="pseudo-select js-pseudo-select modal__select">
            {this.getSelect()}
            <span class="pseudo-select__current">{(this.props.current ? this.props.current.title : "-")}</span>
            <span class="pseudo-select__dropdown">
                {this.getDetails()}
            </span>
        </span>
    }
}

interface IMapStateToProps {
    items: FORMULA_SIMPLE[],
    current: FORMULA,
    city: CITY,
}

const mapStateToProps = (state: IRootReducer): IMapStateToProps => ({
    items: state.formulas.items,
    current: state.formulas.current,
    city: state.common.city
});

export default connect(mapStateToProps)(Fromulas)