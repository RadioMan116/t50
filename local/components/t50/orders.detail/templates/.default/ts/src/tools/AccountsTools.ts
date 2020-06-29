import { AccountsItem } from "@Root/reducers/accounts"



export default class AccountsTools {
    static setArrayValue(currentValuye: string | string[], newValue: string, index: number): string | string[] {
        if (!Array.isArray(currentValuye))
            return newValue

        currentValuye = [...currentValuye]
        while (currentValuye.length < index + 1)
            currentValuye.push("")

        currentValuye[index] = newValue

        return currentValuye
    }

    static isEquals(val1: string | string[], val2: string | string[]): boolean {
        let isArray1 = Array.isArray(val1) ? 1 : 0
        let isArray2 = Array.isArray(val2) ? 1 : 0
        if (isArray1 ^ isArray2)
            return false

        if (!isArray1 && !isArray2)
            return (val1 === val2)

        if (val1.length != val2.length)
            return false

        for (let i = 0; i < val1.length; i++)
            if (val1[i] !== val2[i])
                return false

        return true
    }
}