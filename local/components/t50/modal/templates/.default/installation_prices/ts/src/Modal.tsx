import { Component, h } from "preact"
import { IRootReducer } from "./reducer";
import { connect } from "preact-redux";
import Action from "./actions";
import Select, { SelectItem } from "./components/Select";
import CustomInput from "./CustomInput";

class Modal extends Component<IRootReducer>
{
    private installationId = 0;

    componentDidMount() {
        $(".modal[id='installation_prices']").on("modal_loaded", (event, opts) => {
            this.installationId = 0;

            let data = opts.$orig.context.dataset;
            if( !!data.provider && !!data.service_id){
                Action.reset();
                Action.loadService(data.provider, data.service_id);
            } else {
                Action.initial();
            }

            if( !!data.installation_id )
                this.installationId = data.installation_id

            if (data.custom_input == "Y")
                Action.setCustomInput("");
        });
    }

    componentDidUpdate(){
		window["common"]["jsSelectInit"]();
    }

    changeProvider(provider: SelectItem) {
        Action.initial(provider.val as string);
    }

    changeCategory(category: SelectItem) {
        Action.selectCategoty(this.props.provider, category.val as number);
    }

    selectService(service: SelectItem) {
        Action.selectService(this.props.provider, this.props.category_id, service.val as number);
    }

    sumbit() {
        if( this.props.custom_input !== null && this.props.custom_input.length == 0 )
            return;

        T50PubSub.send("select_installation_service", {
            provider: this.props.provider,
            category_id: this.props.category_id,
            service_id: this.props.service_id,
            price: this.props.price,
            custom_input: this.props.custom_input,
            installation_id: this.installationId,
        });
    }

    // getPrice() {
    //     if (this.props.price == null)
    //         return null;

    //     let price = this.props.price.toLocaleString('ru-RU')

    //     return <label class="form__line">
    //         <span class="form__label">Цена</span>
    //         <div class="form__field">
    //             <input value={price} readOnly class="form__input" type="text" />
    //         </div>
    //     </label>
    // }

    shouldComponentUpdate(nextProps: IRootReducer){
        return ( this.props.custom_input == nextProps.custom_input );
    }

    render() {
        return <div class="modal modal_size_small" id="installation_prices">
            <h3 class="modal__title modal__title_align_center">Выбор поставщика установки</h3>
            <div class="modal__content">
                <form class="form">
                    <label class="form__line">
                        <span class="form__label">Поставщик</span>
                        <Select onSelect={this.changeProvider.bind(this)} value={this.props.provider} items={this.props.providers} class="js-select modal__select" key={Select.key()} />
                    </label>

                    <label class="form__line">
                        <span class="form__label">Категория</span>
                        <Select onSelect={this.changeCategory.bind(this)} value={this.props.category_id} items={this.props.categories} class="js-select modal__select" key={Select.key()} />
                    </label>

                    {this.props.services == null ? null :
                    <label class="form__line">
                        <span class="form__label">Подкатегория</span>
                        <Select onSelect={this.selectService.bind(this)} value={this.props.service_id} items={this.props.services} class="js-select modal__select" key={Select.key()} />
                    </label>
                    }

                    {/* {this.getPrice()} */}

                    <CustomInput />

                    <div class="form__controls form__controls_align_center modal__controls modal__controls_style_separate">
                        <div class="form__control modal__control">
                            <button class="button button_width_full" type="button" disabled={this.props.service_id == null} onClick={this.sumbit.bind(this)}>Выбрать</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    }

}


const mapStateToProps = (state: IRootReducer): IRootReducer => state;

export default connect(mapStateToProps)(Modal)