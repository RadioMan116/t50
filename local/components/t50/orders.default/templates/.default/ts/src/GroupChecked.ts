import Base from "./Base";

export default class GroupChecked extends Base
{
    constructor(){
        super();
        $(".js_gr_checked .js_gr_checked__tigger").click(this.exec.bind(this))
    }

    exec(e: JQueryEventObject){
        let $target = $(e.target);
        let group = $target.data("group");
        let $scope = $target.closest(".js_gr_checked");
        let $checkBoxes = $scope.find("input[type='checkbox']");

        let checked = true;
        if( group == "all" ){
            $checkBoxes = $scope.find("input[type='checkbox']");
            checked = !$checkBoxes.eq(0).prop("checked");
        } else {
            $checkBoxes = $scope.find(`input[type='checkbox'][data-group*='${group}']`);
            let $otherCheckBoxes = $scope.find(`input[type='checkbox'][data-group!='${group}']`);
            this.setChecked($otherCheckBoxes, false);
        }

        this.setChecked($checkBoxes, checked);
    }

    setChecked($checkBoxes: JQuery<Element>, checked: boolean){
        $checkBoxes.attr('data-checked', (checked ? "true" : "false"));
        $checkBoxes.prop('checked', checked);
    }
}