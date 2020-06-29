import { store } from "@Root/Stor";
import { Manager } from "@Root/types";
import { SelectItem } from "@Root/components/Select";

export default class CommonTools
{
    static getSelectItem(val: number|string, arr: SelectItem[] ){
        return arr.find(item => item.val == val)?.title
    }

    static indexArrayToSelectItems(ar: any[] ):SelectItem[]{
        return ar.map(val => ({val: val, title: val}));
    }

    static getManager(managerId: number): string{
        return store.getState().order.managers.find(item => item.id == managerId)?.name
    }

    static getManagersForSelection(): SelectItem[]{
        let managers: SelectItem[] = store.getState().order.managers.map(manager => ({val: manager.id, title: manager.name}))
        return managers;
    }

    static getMultipleUpdateFlag(name: "accounts" | "delivery" | "install", code: string){
        if( ["mkad_km", "vip", "visit_master_km"].indexOf(code) != -1 )
            return {}

        let mode = store.getState().once_supplier[name];
        if( mode === true )
            return {"multiple_update_mode": "Y"}

        return {}
    }
}