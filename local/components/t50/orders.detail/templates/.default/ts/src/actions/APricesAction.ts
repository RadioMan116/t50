import Action from "./Action";
import CommonAction from "./CommonAction";
import ProfitAction from "./ProfitAction";



export default abstract class APricesAction extends Action
{
    protected static fastUpdate(){
        ProfitAction.loadByOrderId()
        Action.updateLogs()
    }
}