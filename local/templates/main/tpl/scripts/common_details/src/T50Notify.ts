type Message = {
    msg?: string,
    isSuccess: boolean,
}
export default class T50Notify {
    private static INSTANCE: T50Notify;
    private block: JQuery;
    private blockText: JQuery;
    private queue: Message[] = []
    private timer = 0
    private running = false

    constructor() {
        this.block = $("#t50_notify")
        this.block.click(() => {
            this.__close()
        })
        this.blockText = this.block.find(".notify_text_block")
    }

    private static getInstance() {
        if (T50Notify.INSTANCE == null)
            T50Notify.INSTANCE = new T50Notify;

        return T50Notify.INSTANCE
    }

    static success(msg?: string) {
        T50Notify.getInstance().__exec({isSuccess: true, msg});
    }

    static error(msg?: string) {
        T50Notify.getInstance().__exec({isSuccess: false, msg});
    }

    // static wait(stop?: true){
    //     let method = ( stop ? "removeClass" : "toggleClass" )
    //     $("body")[method]("cursor_wait")
    // }

    private __exec(message?: Message){
        this.queue.push(message)
        if( this.running )
            this.__close()
        else
            this.__show()
    }

    private __show(){
        let message = this.queue.shift();
        if( message == null )
            return

        this.running = true

        let className = ( message.isSuccess ? "notify_success" : "notify_error" )
        this.block.attr("class", "notify " + className)
        // this.blockText.fadeOut(10)
        this.blockText.text(message.msg ?? "")

        // this.block.slideDown(150, () => {
        //     this.blockText.addClass("notify_text_block_show")
        //     this.blockText.fadeIn(150)
        // })
        this.block.show()
        this.blockText.addClass("notify_text_block_show")

        let closeTimeout = ( message.isSuccess ? 350 : 4500 )
        this.timer = window.setTimeout(() => {
            this.__close()
        }, closeTimeout)
    }

    private __close(){
        if( this.timer > 0 ){
            window.clearTimeout(this.timer)
            this.timer = 0
        }

        // this.blockText.fadeOut(100)
        // this.block.slideUp(300, () => {
        //     this.blockText.removeClass("notify_text_block_show")
        //     this.running = false
        //     this.__show();
        // })
        this.block.hide();
        this.blockText.removeClass("notify_text_block_show")
        this.running = false
        this.__show();
    }
}