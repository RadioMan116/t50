import { render, h } from "preact";
import FormPersons from "./FormPersons";
import App from "./App";


$(document).ready(function(){
    render(<App/>, document.getElementById("order_comment_react"))
});
