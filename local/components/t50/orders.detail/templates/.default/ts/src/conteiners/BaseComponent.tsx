import { Component } from "preact";
import JQueryTools from "@Root/tools/JQueryTools";
import Comparator from "@Root/tools/Comparator";

export default abstract class BaseComponent<T> extends Component<T>
{
	protected jqueryUpdate = false

	componentDidUpdate(){
		this.initJQueryUI()
	}

	shouldComponentUpdate(nextProps: T, nextState?: any){
        return !Comparator.isDeepEquals(this.props, nextProps);
    }

	initJQueryUI() {
		JQueryTools.commonEffectsStack()
		if( this.jqueryUpdate )
			return

		this.jqueryUpdate = true

		JQueryTools.select(this.base)
		JQueryTools.modal()
		JQueryTools.tooltip(this.base)
	}
}
