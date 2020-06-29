import { h, Component } from "preact";
import { store } from "../Stor";
import { changeComment } from "../actions";

type Props = { value: string };
export default class Comment extends Component<Props> {
    state: Props = { value: "" }

    onChange(e: Event) {
        let target = e.target as HTMLInputElement
        this.setState({ value: target.value })
        store.dispatch(changeComment(target.value))
    }

    componentWillReceiveProps(props: Props){
    	this.setState({value: props.value})
    }

    render() {
        return <label class="form__line">
            <span class="form__label">Обоснование</span>
            <textarea name="comment" class="form__textarea" value={this.state.value} onChange={this.onChange.bind(this)}></textarea>
        </label>
    }

}