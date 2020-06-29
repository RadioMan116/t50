import { FORMULA } from "@Root/types";
import { addLog } from "@Root/actions/commonActions";
import API from "@Root/API";

export default class Products
{
    readonly UNIDS_CHUNK_SIZE = 20;

    async updateByFormula(formula: FORMULA){
        let unids: number[] = []
        let selectedPriceIds = this.getSelectedIds();
        if( selectedPriceIds.length > 0 ){ // selected (checbox) products from list
            unids = await this.setFromulaForPricesId(selectedPriceIds, formula);
        } else { // when opening a formula without selecting any products
            unids = await this.getUnidsByFormula(formula)
        }

        if( unids.length == 0 )
            return

        addLog(`------`)


        let chunkUnids: number[];
        let countDone = 0, countAll = unids.length;
        while( unids.length ){
            chunkUnids = unids.splice(0, this.UNIDS_CHUNK_SIZE)
            let result = await API.recaclProducts(chunkUnids)
            if( result ){
                countDone += chunkUnids.length
                addLog(`Успешно обновлено ${countDone}/${countAll}`)
            } else {
                addLog(`Ошибка!`)
                return;
            }
        }
        addLog(`Обновление завершено`)
        T50Notify.success("Обновлено")
    }

    private async setFromulaForPricesId(pricesId: number[], formula: FORMULA): Promise<number[]>{
        addLog(`Выбрано товаров: ${pricesId.length}`)
        addLog(`Устанавливаем формулу...`)
        let answer = await API.setFromulaForProducts(pricesId, formula.id)
        if( !answer.result ){
            addLog(`Ошибка!`)
            T50Notify.error("Ошибка")
            return [];
        }

        addLog(`Формула успешно установлена`)
        addLog(`Найдено ${answer.data.length} товаров с формулой "${formula.title}"`)
        return Promise.resolve(answer.data);
    }

    private async getUnidsByFormula(formula: FORMULA): Promise<number[]>{
        addLog(`Товары не выбраны. Поиск по формуле...`)
        let answer = await API.getUnidsByFormula(formula.id)
        if( !answer.result ){
            addLog(`Ошибка!`)
            T50Notify.error("Ошибка")
            return [];
        }
        addLog(`Найдено ${answer.data.length} товаров с формулой "${formula.title}"`)
        return Promise.resolve(answer.data);
    }

    private getSelectedIds(): number[]{
        let ids = []
        $.each($("input.js_selected_ids:checked"), (index, input) => {
            ids.push(parseInt($(input).val().toString()))
        });
        return ids
    }
}