import { Component, h } from "preact";
import Checkbox from "@Components/Checkbox";
import { SUPPLIER } from "@Root/types";
import { IRootReducer } from "@Root/reducers";
import { connect } from "preact-redux";
import { store } from "@Root/Stor";
import { changeRRCSuppliers } from "@Actions/suppliersActions";


export class Suppliers extends Component<IMapStateToProps>
{
    readonly MAX_COUNT_COLUMNS = 3;
    state: {toggle_all_active: boolean} = {toggle_all_active: false}

    changeActive(item: SUPPLIER, checked: boolean){
        let index = this.props.rrcIds.indexOf(item.id)

        let newRrcIds = [...this.props.rrcIds]
        if( checked ){
            if( index == -1 )
                newRrcIds.push(item.id)
        } else {
            if( index != -1 )
                newRrcIds.splice(index, 1)
        }

        store.dispatch(changeRRCSuppliers(newRrcIds))
    }

    toggleAll(){
        let toggleAllActive = !this.state.toggle_all_active
        this.setState({toggle_all_active: toggleAllActive})
        let newRrcIds = []
        if( toggleAllActive ){
            this.props.items.forEach(item => newRrcIds.push(item.id))
        }
        store.dispatch(changeRRCSuppliers(newRrcIds))
    }

    getItemsChunks() {
        let countInColumn = Math.ceil(this.props.items.length / this.MAX_COUNT_COLUMNS);

        let itemsChunk: SUPPLIER[][] = [];
        let tmp: SUPPLIER[] = [];

        this.props.items.forEach(item => {
            tmp.push(item)
            if (tmp.length == countInColumn) {
                itemsChunk.push(tmp)
                tmp = [];
            }
        })
        if (tmp.length)
            itemsChunk.push(tmp)

        return itemsChunk.map(items => {
            return <div class="showcase__item">
                {items.map(item => {
                    let checked = this.props.rrcIds.includes(item.id)
                    return <div class="filter-panel__item">
                        <Checkbox text={item.title} checked={checked} onClick={checked => {
                            this.changeActive(item, checked)
                        }} />
                    </div>
                })}
            </div>
        });
    }

    render() {
        return <div class="grid-12__col grid-12__col_size_9">
            <div class="modal__subtitle modal__subtitle_size_middle">Продажная цена от поставщика</div>
            <div class="filter-panel">
                <div class="filter-panel__content filter-panel__content_state_editable">
                    <ul class="filter-panel__types">
                        <li class="filter-panel__type">
                            <span class="link link_style_default-trigger" onClick={this.toggleAll.bind(this)}>Все</span>
                        </li>
                    </ul>
                    <div class="filter-panel__list">

                        <div class="showcase showcase_cols_auto">
                            <div class="showcase__list">
                                {this.getItemsChunks()}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}


interface IMapStateToProps {
    items: SUPPLIER[],
    rrcIds: number[],

}

const mapStateToProps = (state: IRootReducer): IMapStateToProps => ({
    items: state.suppliers.items,
    rrcIds: state.suppliers.rrcSuppliers
});

export default connect(mapStateToProps)(Suppliers)