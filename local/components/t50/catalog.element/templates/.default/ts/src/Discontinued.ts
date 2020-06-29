export default class Discontinued {
    private root: JQuery;

    constructor() {
        this.root = $("#discontinued");
        this.root.click((e) => {
            e.preventDefault();
            if (this.root.is(":checked")) {
                ($ as any).fancybox.open({ src: '#universal_comment' });
            } else {
                this.off();
            }
        });
        T50PubSub.subscribe("universal_prompt", this.setDiscounted.bind(this));
    }

    async setDiscounted(comment: string) {
        if (!comment || comment.length < 5)
            return;

        let result = await this.send(true, comment);
        if (result)
            this.root.prop("checked", true)
    }

    async off() {
        let result = await this.send(false, "------------");
        if (result)
            this.root.prop("checked", false)
    }

    async send(discontinued: boolean, comment: string) {
        let product_id = this.root.data("id");
        let answer = await T50Ajax.postJson("catalog.element", "set_discontinued", { product_id, discontinued, comment })
        if (answer.result) {
            T50Notify.success("Обновлено");
            ($ as any).fancybox.close();
        } else {
            T50Notify.error("Ошибка")
        }
        return answer.result;
    }
}