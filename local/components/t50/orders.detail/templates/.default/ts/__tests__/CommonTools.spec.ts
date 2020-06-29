import Tools from "@Root/tools/Tools";

describe('tools', () => {
    const checkFnumIgnoreLocal = (num: number, expected: string) => {
        let result = Tools.fnum(num).replace(/,/g, " ");
        expect(result).toEqual(expected)
    }
    it('check Tools.fnum()', () => {
        checkFnumIgnoreLocal(1000, "1 000")
        checkFnumIgnoreLocal(1135000, "1 135 000")
        checkFnumIgnoreLocal(100, "100")
        checkFnumIgnoreLocal(null, "")
        checkFnumIgnoreLocal(("153153" as any), "153 153")
    });
});

