import { render, h } from 'preact';
import {Provider} from "preact-redux"
import { store } from "./Stor";
import globals from './globals';
import App from '@Conteiners/App';

$(document).ready(() => {
    render(<Provider store={store}><App /></Provider>, document.getElementById("formula_panel_wrap"));
});

// export default globals;