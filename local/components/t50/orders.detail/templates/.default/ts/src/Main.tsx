import { render, h } from 'preact';
import {Provider} from "preact-redux"
import { store } from "./Stor";
import globals from './globals';
import CommonAction from './actions/CommonAction';
import Basket from './conteiners/Basket';
import Accounts from './conteiners/Accounts';
import Client from './conteiners/Client';
import Comments from './conteiners/Comments';
import Delivery from './conteiners/Delivery';
import Docs from './conteiners/Docs';
import Fine from './conteiners/Fine';
import Deduction from './conteiners/Deduction';
import Header from './conteiners/Header';
import Installation from './conteiners/Installation';
import Profit from './conteiners/Profit';
import Reclamation from './conteiners/Reclamation';
import Statistic from './conteiners/Statistic';
import FlagOnceSupplier from './conteiners/FlagOnceSupplier';
import AccountAction from './actions/AccountAction';

$(document).ready(() => {
    (async () => {
        await CommonAction.loadStatic();
        await CommonAction.loadOrder();
        await CommonAction.loadAllPrices();
        AccountAction.loadAll();
        CommonAction.updateLogs();
    })()

    render(<Provider store={store}><Header /></Provider>, document.getElementById("react_header"));
    render(<Provider store={store}><Statistic /></Provider>, document.getElementById("react_statistic"));
    render(<Provider store={store}><Basket /></Provider>, document.getElementById("react_basket"));
    render(<Provider store={store}><Accounts /></Provider>, document.getElementById("react_accounts"));
    render(<Provider store={store}><Client /></Provider>, document.getElementById("react_client"));
    render(<Provider store={store}><Comments /></Provider>, document.getElementById("react_comments"));
    render(<Provider store={store}><Delivery /></Provider>, document.getElementById("react_delivery"));
    render(<Provider store={store}><Docs /></Provider>, document.getElementById("react_docs"));
    render(<Provider store={store}><Fine /></Provider>, document.getElementById("react_fine"));
    render(<Provider store={store}><Deduction /></Provider>, document.getElementById("react_deduction"));
    render(<Provider store={store}><Installation /></Provider>, document.getElementById("react_installation"));
    render(<Provider store={store}><Profit /></Provider>, document.getElementById("react_profit"));
    render(<Provider store={store}><Reclamation /></Provider>, document.getElementById("react_reclamation"));

    render(<Provider store={store}><FlagOnceSupplier block="accounts" /></Provider>, document.getElementById("react_accounts__flag_one_supplier"));
    render(<Provider store={store}><FlagOnceSupplier block="delivery" /></Provider>, document.getElementById("react_delivery__flag_one_supplier"));
    render(<Provider store={store}><FlagOnceSupplier block="install" /></Provider>, document.getElementById("react_installation__flag_one_supplier"));
});

export default globals;