import { h } from "preact";

export default function(props: {name: string, class: string }){
    return <svg class={props.class}>
        <use xlinkHref={`/local/templates/main/assets/images/icon.svg#${props.name}`}></use>
    </svg>
}