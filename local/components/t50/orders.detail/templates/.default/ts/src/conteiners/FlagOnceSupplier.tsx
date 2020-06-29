import { Component, h } from "preact";
import Checkbox from "../components/Checkbox";
import { IRootReducer } from "@Root/reducers";
import { connect } from "preact-redux";
import OnceSupplierAction from "@Root/actions/OnceSupplierAction";
import { IOnceSupplier } from "@Root/reducers/once_supplier";

type Props = {block?: "delivery" | "install" | "accounts"}
class FlagOnceSupplier extends Component<IMapStateToProps & Props>
{
    state: Props = {}

    onChange(value: boolean) {
        if( value && !confirm("Включить режим множественного обновления?") )
            return false;

        switch(this.state.block){
            case "delivery":
                OnceSupplierAction.switchDelivery(value)
            break;
            case "accounts":
                OnceSupplierAction.switchAccount(value)
            break;
            case "install":
                OnceSupplierAction.switchInstall(value)
            break;
        }
        return true;
    }

    componentDidMount(){
        this.setState({block: this.props.block})
    }

    render() {
        let checked = this.props[this.state.block];
        return <Checkbox  checked={checked} awaitConfirm={true} onClick={this.onChange.bind(this)} text="Один поставщик"/>
    }
}

type IMapStateToProps = IOnceSupplier

const mapStateToProps = (state: IRootReducer): IMapStateToProps => state.once_supplier;

export default connect(mapStateToProps)(FlagOnceSupplier)