import { Component, h } from "preact"
import { VALID_KEYS } from "./types_consts";
import Header from "./components/Header";
import InputAvail from "./components/InputAvail";
import InputPrice from "./components/InputPrice";
import InputDate from "./components/InputDate";
import { IRootReducer } from "./reducer";
import { connect } from "preact-redux";
import { store } from "./Stor";
import { loadAll } from "./actions";
import Comment from "./components/Comment";

class Modal extends Component<IRootReducer>
{
    constructor(props){
        super(props)
        $(".modal[id='manual']").on( "modal_loaded", (event, opts) => {
            store.dispatch(loadAll(opts.$orig.data()))
        });
        $(".modal[id='manual']").on( "modal_close", (event, opts) => {
            store.dispatch(loadAll({
                comment: "",
                date_end: "",
                modal_key: null,
                value: 0
            }))
        });
    }

    isValidKey(): boolean{
        if ( this.props.modal_key == null )
            return false

        return VALID_KEYS.indexOf(this.props.modal_key) != -1
    }

    onSubmit(e: Event){
        e.preventDefault();
        T50PubSub.send("manual_modal_submit", store.getState())
        console.log("store.getState()", store.getState());
    }

    render() {
        if( !this.isValidKey() ){
            ($ as any).fancybox.close();
            return
        }

        return <div>
            <Header modalKey={this.props.modal_key}/>

            <div class="modal__content">
                <form class="form" action="" method="post" onSubmit={this.onSubmit.bind(this)}>

                    {this.props.modal_key.indexOf("avail") != -1 ? <InputAvail value={this.props.value}/> : <InputPrice value={this.props.value} modal_key={this.props.modal_key}/>}

                    <InputDate value={this.props.date_end} modalKey={this.props.modal_key}/>

                    <Comment value={this.props.comment}/>

                    <div class="form__controls form__controls_align_center modal__controls">
                        <div class="form__control modal__control">
                            <button class="button button_width_full" type="submit" >Сохранить</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    }
}


const mapStateToProps = (state: IRootReducer): IRootReducer => state;

export default connect(mapStateToProps)(Modal)