export default class Basket
{
    private $priceTextBlock: JQuery
    private $shopsDataSelect: JQuery
    private data: {};

    constructor(){
        this.$priceTextBlock = $("#addToCart .js_product_price");
        $("#add_to_cart_submit").click(this.submit.bind(this));
        this.$shopsDataSelect = $("#addToCart .js_product_shops");
        this.$shopsDataSelect.change(this.selectShop.bind(this))
        this.selectShop();
    }

    selectShop(){
        this.data = this.$shopsDataSelect.find("option:selected").data();
        if( this.data != null )
            this.$priceTextBlock.text(this.data["price"])
    }

    getOrderId(){
        let order_id = parseInt($("#addToCart .js_exist_order").val().toString());

        if( order_id > 0 )
            this.data["order_id"] = order_id;
    }

    async submit(e: Event){
        e.preventDefault();
        if( this.data == null )
        T50Notify.error("Ошибка")

        this.getOrderId();

        let answer = await T50Ajax.postJson<{orderId: number}>("catalog.element", "add_to_basket", this.data)
        if( !answer.result ){
            T50Notify.error("Ошибка")
            console.error(answer);
            return
        }

        if( this.data["order_id"] > 0 ){
            T50Notify.success(`Товар добавлен в заказ №${answer.data.orderId}`)
        } else {
            T50Notify.success(`Создан заказ №${answer.data.orderId}`)
        }
        setTimeout(() => {
            document.location.href = `/orders/${answer.data.orderId}/`
        }, 1000)
    }
}