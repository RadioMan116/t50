import { InstallationItem } from "@Root/reducers/installation";

export default class InstallationTools
{
    static getBasketId(id: number, items: InstallationItem[]){
        return items.find(item => item.id == id)?.basket_id ?? 0
    }
}