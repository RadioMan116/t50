import { SelectItem } from "@Root/components/Select";
import { MONTHS } from "@Root/consts";

export default class ProfitTools
{
    static getMonths(): SelectItem[]{
        let result: SelectItem[] = []
        for(let num in MONTHS){
            result.push({
                title: MONTHS[num],
                val: num,
            })
        }
        return result
    }
}