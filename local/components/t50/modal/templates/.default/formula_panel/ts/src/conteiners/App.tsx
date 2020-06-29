import { Component, h } from "preact";
import Suppliers from "@Root/conteiners/Suppliers";
import PriceType from "./PriceType";
import Checkbox from "../components/Checkbox";
import Fromulas from "./Formulas";
import Parameters from "./Parameters";
import { IRootReducer } from "@Root/reducers";
import { connect } from "preact-redux";
import { changeCalcFromSalePrice, changeCity, addLog, clearLog } from "@Actions/commonActions";
import { FORMULA, CITY, FORMULA_UPDATE_DATA } from "@Root/types";
import Info from "@Root/components/Info";
import { store } from "@Root/Stor";
import API from "@Root/API";
import { changeFormula } from "@Root/actions/formulasActions";
import PanelInfo from "@Root/components/PanelInfo";
import { MAKE_NEW_EMPTY_FORMULA } from "@Root/consts";
import Products from "@Root/services/Products";


class App extends Component<IMapStateToProps>
{
    private products: Products

    constructor(props: IMapStateToProps){
        super(props)
        this.products = new Products
    }

    componentWillMount() {
        let cityMatch = document.location.search.match(/city=(MSK|SPB)/);
        if (cityMatch != null){
            changeCity(cityMatch[1] as CITY)
        }
    }

    componentDidMount(){
        $("#formula_panel").on( "modal_loaded", (event, opts) => {
            if( $(this).data("static_loaded") )
                return

            API.loadInfo(this.props.city);
            $(this).data("static_loaded", true)
        });

        $("#formula_panel").on( "modal_close", (event, opts) => {
            clearLog()
        });
    }


    changeComment(e: Event) {
        let current = this.props.currentFormula
        if (current == null)
            return

        let input = e.target as HTMLInputElement
        current.comment = input.value
        store.dispatch(changeFormula(current))
    }

    private getData(): FORMULA_UPDATE_DATA {
        let state = store.getState();
        return {
            title: state.formulas.current.title,
            city: this.props.city,
            comment: state.formulas.current.comment,
            price_type: state.common.priceType,
            calc_from_sale: state.common.calcFromSalePrice,
            suppliers_calc_from_sale: state.suppliers.rrcSuppliers,
            check_rrc: state.parameters.check_rrc,
            parameters: state.parameters.blocks
        }
    }

    async update() {
        if (this.props.currentFormula == null)
            return;

        let data = this.getData();
        data.id = this.props.currentFormula.id
        let answer = await API.updateFormula(data);
        if( answer.result ){
            addLog(`Обновлена формула "${this.props.currentFormula.title}"`)
            this.products.updateByFormula(this.props.currentFormula)
        } else {
            addLog(`Ошибка обновления формулы "${this.props.currentFormula.title}"!`)
            T50Notify.error("Ошибка")
            console.log(answer);
        }

    }

    async create() {
        if (this.props.currentFormula == null) {
            store.dispatch({type: MAKE_NEW_EMPTY_FORMULA})
            return;
        }

        let answer = await API.updateFormula(this.getData());
        if (answer.result) {
            addLog(``)
            API.loadInfo(this.props.city);
            let id = answer.data;
            addLog(`Создана новая формула "${this.props.currentFormula.title}"`)
            await API.loadFormula(id)
            this.products.updateByFormula(this.props.currentFormula)
        } else {
            addLog(`Ошибка при создании формулы!`)
            T50Notify.error("Ошибка")
            console.log(answer);
        }
    }

    cancel() {
        $["fancybox"].close()
    }

    showWait() {
        if (this.props.wait)
            return <div class="form__controls_align_center modal__controls"><div class="formatted-text"><p>wait ...</p></div></div>
    }

    render() {
        let { currentFormula, wait } = this.props
        let comment = (currentFormula == null ? "" : currentFormula.comment)
        return <div class="modal modal_size_big" id="formula_panel">
            <h3 class="modal__title modal__title_align_center">Действия с выбранными товарами из магазина</h3>
            <div class="modal__content">

                <div class="form">
                    <div class="grid-12__row">
                        <PriceType />
                        <Suppliers />
                    </div>

                    <div class="modal__title">Формула цены</div>

                    <div class="form__line">
                        <Checkbox text="Расчет от продажной цены" checked={this.props.calcFromSalePrice} onClick={changeCalcFromSalePrice} />
                    </div>

                    <label class="form__line">
                        <label class="form__label">Готовые формулы</label>
                        <Fromulas />
                    </label>


                    <div class="form__line">
                        <Parameters />
                    </div>

                    <Info {...currentFormula} />

                    <div class="grid-12__row">
                        <div class="grid-12__col grid-12__col_size_8">
                            {this.props.currentFormula ?
                                <label class="form__line"><span class="form__label">Обоснование</span>
                                    <input value={comment} class="form__input" type="text" onInput={this.changeComment.bind(this)} />
                                </label>
                                : null}
                        </div>
                        <div class="grid-12__col grid-12__col_size_4">
                            <label class="form__line">
                                <span class="form__label">&nbsp;</span>
                                <button class="button button_width_full button_style_dark button_type_concrete"
                                    disabled={wait}
                                    onClick={this.create.bind(this)}
                                >Добавить в формулы</button>
                            </label>
                        </div>
                    </div>

                    <div class="form__controls form__controls_align_center modal__controls">
                        <div class="form__control modal__control">
                            <button class="button button_width_full button_type_concrete"
                                disabled={currentFormula == null || wait}
                                onClick={this.update.bind(this)}>Применить</button>
                        </div>
                        <div class="form__control modal__control">
                            <button class="button button_style_default button_width_full button_type_concrete" type="button"
                                onClick={this.cancel.bind(this)}>Отменить</button>
                        </div>
                    </div>

                    {/* {this.showWait()} */}

                    <PanelInfo messages={this.props.logs} />

                </div>
            </div>
        </div>
    }
}


interface IMapStateToProps {
    calcFromSalePrice: boolean,
    currentFormula: FORMULA,
    wait: boolean,
    city: CITY,
    logs: string[],
}

const mapStateToProps = (state: IRootReducer): IMapStateToProps => ({
    calcFromSalePrice: state.common.calcFromSalePrice,
    currentFormula: state.formulas.current,
    wait: state.common.loading_status,
    city: state.common.city,
    logs: state.common.logs
});

export default connect(mapStateToProps)(App)