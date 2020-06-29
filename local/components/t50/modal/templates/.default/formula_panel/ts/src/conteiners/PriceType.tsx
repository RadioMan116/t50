import { Component, h } from "preact";
import RadioButtons from "@Components/RadioButtons";
import { PRICE_TYPE } from "@Root/types";
import { IRootReducer } from "@Root/reducers";
import { connect } from "preact-redux";
import { changePriceType } from "@Actions/commonActions";


class PriceType extends Component<IMapStateToProps>
{

    render() {
        return <div class="grid-12__col grid-12__col_size_3">
            <div class="modal__subtitle modal__subtitle_size_middle">Ценообразование</div>
            <div class="filter-panel">
                <div class="filter-panel__content filter-panel__content_state_editable">
                    <div class="filter-panel__list">
                        <div class="showcase">
                            <div class="showcase__list">
                                <div class="showcase__item">
                                    <RadioButtons
                                        data={[{value: "rrc", title: "РРЦ"}, {value: "free", title: "Свободное"}]}
                                        name="price_type"
                                        value={this.props.value}
                                        itemClass="filter-panel__item"
                                        onChange={changePriceType}
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}

interface IMapStateToProps {
    value: PRICE_TYPE
}

const mapStateToProps = (state: IRootReducer): IMapStateToProps => ({
    value: state.common.priceType
});

export default connect(mapStateToProps)(PriceType)