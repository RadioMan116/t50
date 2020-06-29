import { render, h } from "preact";
import App from "./App";


$(document).ready(function(){
    render(<App/>, document.getElementById("email_editor_react"))
});
