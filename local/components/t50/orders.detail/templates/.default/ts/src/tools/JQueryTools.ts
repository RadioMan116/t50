export default class JQueryTools
{
    static select(el: HTMLElement){
        var $select = $(el).find('.js-select:visible') as any;
		$select.select2({
			minimumResultsForSearch: -1
		});
    }

    static modal(){
        var $modal = $('.js-modal');
		$modal["fancybox"]({
			afterShow: function () {
				JQueryTools.commonEffectsStack()
			},
			afterLoad: function (){
				this.$content.trigger("modal_loaded", this.opts);
			},
			afterClose: function (){
				this.$content.trigger("modal_close", this.opts);
			}
		});
    }

    static tooltip(el: HTMLElement){
		try{
			$(el).find('.js-tooltip')["tooltipster"]('destroy');
		} catch(e){}

        $(el).find('.js-tooltip')["tooltipster"]({
			minWidth: 200,
			contentCloning: false,
			trigger: 'click',
			interactive: true
		});
    }

	static commonEffectsStack(){
		window["common"]["jsDate"]();
		window["common"]["jsTime"]();
		window["common"]["jsSelectInit"]();
	}
}