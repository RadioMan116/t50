import { render, h } from "preact";
import Modal from "./Modal";


$(document).ready(function(){
    render(<Modal/>, document.getElementById("analogs"))
});
