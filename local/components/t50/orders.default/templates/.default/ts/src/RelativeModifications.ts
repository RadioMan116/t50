import GroupChecked from "./GroupChecked";
import Base from "./Base";

export default class RelativeModifications extends Base
{
    constructor(){
        super()
        this.financeAndStatus();
    }

    financeAndStatus(){
        let groupChecked = this.get<GroupChecked>(GroupChecked);
        $("input[name='date_account_from'], input[name='date_account_to']").change(() => {
            groupChecked.setChecked($("input[name='status[]']"), false);
            groupChecked.setChecked($("input[name='status[]'][data-code='wait_payment']"), true);
        });
    }
}