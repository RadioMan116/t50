import CommonData from "./CommonData";

export default class Files
{
    private $blockFiles: JQuery;
    private $inputFile: JQuery;

    constructor(){
        this.$blockFiles = $(".js-block_files");
        this.$inputFile = $(".js-input-file");
        $(".js-file-upload-trigger").click(this.upload.bind(this));
        this.$blockFiles.on("click", ".js-news_file_remove", this.removeFile.bind(this));

        this.$inputFile.change(this.submit.bind(this))
    }

    async submit(e: Event){
        let formData = new FormData()
        formData.append('file', (this.$inputFile[0] as HTMLInputElement).files[0]);
        formData.append('id', this.$inputFile.data("id"));
        let html = await T50Ajax.postFormDataHtml("news.edit", "upload_file", formData);
        this.updateList(html)
        this.$inputFile.val("")
    }

    upload(e: Event) {
        e.preventDefault()
        this.$inputFile.trigger("click")
    }

    async removeFile(e: Event) {
        let ID = CommonData.getId();
        e.preventDefault()
        if( !confirm("Удалить файл?") )
            return;

        if( !(ID > 0) )
            throw new Error("not defined commonData.ID");

        let prop_id =  parseInt($(e.currentTarget).data("prop_id"));
        if( !(prop_id > 0) )
            throw new Error("not defined prop_id");

        let html = await T50Ajax.postHtml("news.edit", "delete_file", {id: ID, prop_id});
        this.updateList(html)
    }

    private updateList(html: string){
        if( html?.length == 0 )
            return

        this.$blockFiles.html(html)
    }
}