import { h, Component } from "preact";
import { IRootReducer } from "@Root/reducers";
import { connect } from "preact-redux";
import SvgIcon from "@Components/SvgIcon"
import BaseComponent from "./BaseComponent";
import DocsAction from "@Root/actions/DocsAction";
import { IDocs } from "@Root/reducers/docs";
import { OrderId, DocFile } from "@Root/types";
import { IAccounts } from "@Root/reducers/accounts";
import { BasketItem } from "@Root/reducers/basket";
import BasketTools from "@Root/tools/BasketTools";
import { IClient } from "@Root/reducers/client";
import { SelectItem } from "@Root/components/Select";
import DateTimePicker from "@Root/components/DateTimePicker";
import CommonAction from "@Root/actions/CommonAction";

class Docs extends BaseComponent<IMapStateToProps>
{
    private fileInput: HTMLInputElement

    componentDidMount() {
        DocsAction.loadAll(this.props.order_id)
        $(this.fileInput).change(() => {
            let formData = new FormData()
            formData.append('file', $(this.fileInput)[0].files[0]);
            formData.append('type', this.fileInput.name);
            formData.append('order_id', this.props.order_id.toString());
            DocsAction.submitFormData(formData)
            this.fileInput.value = ""
        })
    }

    removeFile(type: keyof IDocs, file: DocFile, e: Event) {
        e.preventDefault()
        if (confirm("Удалить файл?"))
            DocsAction.removeFileById(file.id, this.props.order_id, type)
    }

    getItems(name: keyof IDocs) {
        let docs = this.props[name] ?? []
        return docs.map(file => {
            return <li class="files-list__item">
                <a href={file.path} target="_blank" class="files-list__link">{file.title}</a>
                {file.can_delete ?
                    <a href="#" class="files-list__remove" onClick={this.removeFile.bind(this, name, file)}>
                        <SvgIcon name="icon_delete" class="files-list__remove-icon" />
                    </a>
                    : null}
            </li>
        })
    }

    upload(name: keyof IDocs, e: Event) {
        e.preventDefault()
        this.fileInput.name = name
        $(this.fileInput).trigger("click")
    }

    getUploadLink(code: keyof IDocs) {
        return <div class="files-field__controls">
            <div class="files-field__control">
                <a class="link link_style_default-trigger" href="#" onClick={this.upload.bind(this, code)}>Загрузить</a>
            </div>
        </div>
    }

    getLinksByTemplate() {
        let urlPrefix = `/orders/${this.props.order_id}/docs`
        return <div class="doc-group__group">
            <div class="doc-group__label">Шаблоны</div>

            <div class="doc-group__item">
                <a class="link link_style_classic" target="_blank" href={`${urlPrefix}/invoice/?prepayment=Y`}>Счет на предоплату</a>
            </div>
            <div class="doc-group__item">
                <a class="link link_style_classic" target="_blank" href={`${urlPrefix}/invoice/`}>Счет на отгрузку</a>
            </div>
            <div class="doc-group__item">
                <a class="link link_style_classic" target="_blank" href={`${urlPrefix}/offer/`}>Коммерческое предложение</a>
            </div>
            <div class="doc-group__item">
                <a class="link link_style_classic" target="_blank" href={`${urlPrefix}/client_message/`}>Сообщение покупателю</a>
            </div>
            {this.props.uric ?
                <div class="doc-group__item">
                    <a class="link link_style_classic" target="_blank" href={`${urlPrefix}/treaty_legal_persons/`}>Договор для юридических лиц</a>
                </div>
                :
                <div class="doc-group__item">
                    <a class="link link_style_classic" target="_blank" href={`${urlPrefix}/treaty_physical_persons/`}>Договор для физических лиц</a>
                </div>
            }
        </div>
    }

    setDateInvoice(e: Event){
        let value = (e.target as HTMLInputElement).value
        if( value?.length )
            CommonAction.setValue("date_invoice", value)
    }

    render() {
        return <div class="doc-group">
            <input type="file" name="company_card" class="hidden" ref={ref => this.fileInput = ref} />
            <div class="doc-group__inner">
                <div class="doc-group__col">
                    {this.getLinksByTemplate()}
                </div>
                <div class="doc-group__col">
                    <div class="doc-group__group">
                        <div class="doc-group__label">Карточка предприятия</div>
                        <div class="files-field">
                            <input type="file" class="hidden" />
                            <div class="files-field__list">
                                <ul class="files-list">{this.getItems("company_card")}</ul>
                            </div>
                            {this.getUploadLink("company_card")}
                        </div>
                    </div>
                    <div class="doc-group__group">
                        <div class="doc-group__label">Счет на предоплату наш</div>
                        <div class="files-field">
                            <input type="file" class="hidden" />
                            <div class="files-field__list">
                                <ul class="files-list">{this.getItems("our_prepayment_invoice")}</ul>
                            </div>
                            {this.getUploadLink("our_prepayment_invoice")}
                        </div>
                    </div>
                    <div class="doc-group__group">
                        <div class="doc-group__label">Счет на предоплату партнеров</div>
                        <div class="files-field">
                            <input type="file" class="hidden" />
                            <div class="files-field__list">
                                <ul class="files-list">{this.getItems("partners_prepayment_invoice")}</ul>
                            </div>
                            {this.getUploadLink("partners_prepayment_invoice")}
                        </div>
                    </div>
                </div>
                <div class="doc-group__col">
                    <div class="doc-group__group">
                        <div class="doc-group__label">Доверенность на отгрузку в ТК</div>
                        <div class="files-field">
                            <input type="file" class="hidden" />
                            <div class="files-field__list">
                                <ul class="files-list">{this.getItems("proxy_shipment_tk")}</ul>
                            </div>
                            {this.getUploadLink("proxy_shipment_tk")}
                        </div>
                    </div>
                    <div class="doc-group__group">
                        <div class="doc-group__label">Договор</div>
                        <div class="files-field">
                            <input type="file" class="hidden" />
                            <div class="files-field__list">
                                <ul class="files-list">{this.getItems("contract")}</ul>
                            </div>
                            {this.getUploadLink("contract")}
                        </div>
                    </div>
                    <div class="doc-group__group">
                        <div class="doc-group__label">Заказ-наряд</div>
                        <div class="files-field">
                            <input type="file" class="hidden" />
                            <div class="files-field__list">

                                <ul class="files-list">{this.getItems("purchase_order")}</ul>
                            </div>
                            {this.getUploadLink("purchase_order")}
                        </div>
                    </div>
                </div>
                <div class="doc-group__col">
                    <div class="doc-group__group">
                        <div class="doc-group__label">Доверенность на получение груза</div>
                        <div class="files-field">
                            <input type="file" class="hidden" />
                            <div class="files-field__list">

                                <ul class="files-list">{this.getItems("proxy_receipt_goods")}</ul>
                            </div>
                            {this.getUploadLink("proxy_receipt_goods")}
                        </div>
                    </div>
                </div>
                <div class="doc-group__col">
                    <label class="form__line order__field order__field_size_xxl">
                        <div class="form__label">Дата выставления счета</div>
                        <DateTimePicker type="date" value={this.props.date_invoice} onChange={this.setDateInvoice.bind(this)}/>
                    </label>
                </div>
            </div>
        </div>
    }
}


type IMapStateToProps = IDocs & OrderId & { uric: boolean } & { date_invoice: string }

const mapStateToProps = (state: IRootReducer): IMapStateToProps => {
    return {
        ...state.docs,
        order_id: state.order.order_id,
        uric: state.client.is_entity,
        date_invoice: state.order.date_invoice
    }
};

export default connect(mapStateToProps)(Docs)