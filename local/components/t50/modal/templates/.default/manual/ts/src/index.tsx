import { render, h } from "preact";
import {Provider} from "preact-redux"
import Modal from "./Modal";
import { store } from "./Stor";


$(document).ready(function(){
    render(<Provider store={store}><Modal /></Provider>, document.getElementById("manual"))
});
