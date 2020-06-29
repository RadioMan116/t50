import { SelectItem } from "@Root/components/Select";

export default class DeliveryTools
{
    static getTimeVal(time: string, timeRange: string[]): string{
        let regexp = /[^\d]/g
        for(let i = 0; i < timeRange.length; i++){
            if( timeRange[i].replace(regexp, "") == time?.replace(regexp, "") )
                return timeRange[i];
        }
        return null
    }
}