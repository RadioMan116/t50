export default class CommonData
{
    private static ROOT = "NEWS_COMMON_DATA";

    static getId(){
        let value = window[CommonData.ROOT]?.ID;
        value = parseInt(value);
        if( value > 0 )
            return value;
        return 0;
    }

    static getGroups(){
        let value = window[CommonData.ROOT]?.GROUPS;
        if( !Array.isArray(value) )
            return [];

        return value.map(val => parseInt(val)).filter(val => val > 0);
    }
}