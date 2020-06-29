import { MONTHS } from "@Root/consts"

export default class Tools
{
    static fnum(num?: number|string):string{
        if( num == null )
            return ""
        if( typeof num === 'string' )
            num = parseInt(num)
        return num.toLocaleString('ru-RU')
    }

    static getMonthsArr(){
        let months = []
        for(let num in MONTHS)
            months.push({val: num, title: MONTHS[num]})

        return months
    }

    static alt(value: any, alternative: any){
        if( value == null )
            return alternative;

        if( value == 0 || value.toString().length == 0 )
            return alternative

        return value;
    }

    static isEmptyList(value?: any[]){
        return ( value == null || value.length == 0 );
    }
}