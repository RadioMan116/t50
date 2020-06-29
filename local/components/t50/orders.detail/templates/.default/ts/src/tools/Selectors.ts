export default class Selectors
{
    static updateStateExcept(newState: {}, oldSate: {}, expectFields: string[]){
        let state = {...newState}
        expectFields.forEach(expectField => {
            state[expectField] = oldSate[expectField]
        })
        return state
    }
}