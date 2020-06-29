export default class Comparator
{
    static isDeepEquals(a: any, b: any){
        // console.log(a, b);

        if( (a === null) != (b === null) )
            return false;

        if( typeof a != typeof b )
            return false;

        if( typeof a == "object" )
            return Comparator.isObjectsEquals(a, b);

        if( Array.isArray(a) != Array.isArray(b) )
            return false;

        if( Array.isArray(a) )
            return Comparator.isArraysEquals(a, b);

        return a === b;
    }

    static isObjectsEquals(a: Object, b: Object){
        const aKeys = Object.keys(a).sort();
        const bKeys = Object.keys(b).sort();

        if (!Comparator.isArraysEquals(aKeys, bKeys)) {
                return false;
        }

        return aKeys.every(key => {
            const aVal = a[key];
            const bVal = b[key];

            if (aVal === bVal) {
                return true;
            }

            return Comparator.isDeepEquals(aVal, bVal)
        });
    }

    static isArraysEquals(a: any[], b: any[]){
        if( a.length != b.length )
            return false;

        for(let i = 0; i < a.length; i++) {
            if( !Comparator.isDeepEquals(a[i], b[i]) )
                return false;
        }
        return true;
    }
}