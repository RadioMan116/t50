import { Component, h } from "preact";

interface Props
{
    messages: string[]
}
export default class PanelInfo extends Component<Props>
{
    private textarea: HTMLTextAreaElement;

    componentDidUpdate(){
        this.textarea.scrollTop = this.textarea.scrollHeight;
    }

    render(){
        let messages = [...this.props.messages];
        let messagesStr = messages.join("\n")

        return <div class="form__controls form__controls_align_center modal__controls">
            <textarea ref={ref => this.textarea = ref} class="form__textarea  form__textarea_size_normal" value={messagesStr} />
        </div>
    }
}