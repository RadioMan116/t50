import AccountsTools from "@Root/tools/AccountsTools";
import { AccountsItem } from "@Root/reducers/accounts";

describe('AccountsAccountsTools', () => {
    it('check AccountsTools.setArrayValue()', () => {
        expect(AccountsTools.setArrayValue("test", "test1", 0)).toEqual("test1")
        expect(AccountsTools.setArrayValue(["test"], "test1", 2)).toEqual(["test", "", "test1"])
        expect(AccountsTools.setArrayValue(["test", "test1", "test2", "test3" ], "test5", 2)).toEqual(["test", "test1", "test5", "test3" ])
        expect(AccountsTools.setArrayValue(["test", "test1", "test2", "test3" ], "test1", 2)).toEqual(["test", "test1", "test1", "test3"])
        expect(AccountsTools.setArrayValue(["test", "test1", "test2", "test3" ], "", 2)).toEqual(["test", "test1", "", "test3"])
        expect(AccountsTools.setArrayValue(["test"], "test1", 4)).toEqual(["test", "", "", "", "test1"])
        expect(AccountsTools.setArrayValue(["test", "test1", "test2", "test3", "test4", "test5" ], "", 4)).toEqual(["test", "test1", "test2", "test3", "", "test5"])

        let immutable = ["test"]
        expect(AccountsTools.setArrayValue(immutable, "", 1)).toEqual(["test", ""])
        expect(immutable).toEqual(["test"])
    });

    it('check AccountsTools.compareValues()', () => {
        expect(AccountsTools.isEquals("test", "test1")).toBeFalsy()
        expect(AccountsTools.isEquals("test", ["test"])).toBeFalsy()
        expect(AccountsTools.isEquals(["test"], "test")).toBeFalsy()
        expect(AccountsTools.isEquals("test1", "test1")).toBeTruthy()
        expect(AccountsTools.isEquals(["test1"], ["test1"])).toBeTruthy()
        expect(AccountsTools.isEquals(["test1"], ["tesT1"])).toBeFalsy()
        expect(AccountsTools.isEquals(["test1"], ["test1", "test1"])).toBeFalsy()
    });


});

