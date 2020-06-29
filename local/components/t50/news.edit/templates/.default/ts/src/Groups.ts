import CommonData from "./CommonData";

export default class Groups
{
    private $modal: JQuery;
    private $selecte: JQuery;
    private groups: number[] = []

    constructor(){
        this.$modal = $("#group_selection");
        this.$selecte = $(".js_selected_groups");
        this.$modal.find("input[name='all_groups']").click(this.selectAll.bind(this))
        this.$modal.find("button").click(this.submit.bind(this))
        this.setGroups(CommonData.getGroups());

        this.$selecte["select2"]({
			tags: true,
			multiple: true,
			allowclear: true,
			minimumResultsForSearch: -1,
			theme: 'tags-list',
			dropdownCssClass: 'hidden',
		}).on('select2:unselect', (e) => {
            let groups = this.groups.filter( val => val != e.params.data.id)
            this.setGroups(groups);
            return false
        });
    }

    selectAll(e: Event){
        let checked = (e.target as HTMLInputElement).checked
        this.setChecked(this.$modal.find("input[type='checkbox']"), checked);
    }

    setGroups(groups: number[]){
        this.groups = groups;
        let checkboxes = this.$modal.find("input[type='checkbox']");
        this.$selecte.val(null).trigger('change');

        $.each(checkboxes, (index, item: HTMLInputElement) => {
            let value = parseInt(item.value);
            if( this.groups.indexOf(value) != -1 ){
                this.setChecked($(item), true)
                let title = $(item).parent().find("label").text();
                var newOption = new Option(title, `${value}`, true, true);
                this.$selecte.append(newOption)
            } else {
                this.setChecked($(item), false)
            }
        })
    }

    async submit(){
        let checkboxes = this.$modal.find("input[type='checkbox']:checked");
        let groups = []
        $.each(checkboxes, (index, input: HTMLInputElement) => {
            groups.push(input.value)
        });
        ($ as any).fancybox.close();
        this.setGroups(groups);
    }

    setChecked($checkbox: JQuery<Element>, checked: boolean){
        $checkbox.attr('data-checked', (checked ? "true" : "false"));
        $checkbox.prop('checked', checked);
    }
}