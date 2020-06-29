export default class Base
{
    private static container: {[code: string]: Base} = {};

    constructor(){
        Base.container[this.constructor.name] = this;
    }

    get<T extends Base>(constructor: new () => T): T{
        if( !Base.container[constructor.name] )
            Base.container[constructor.name] = new constructor;

        return Base.container[constructor.name] as T;
    }
}