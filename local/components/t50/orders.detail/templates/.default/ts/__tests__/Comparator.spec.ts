import Comparator from "@Root/tools/Comparator";

describe('Comparator', () => {

    it('check simple', () => {
        expect(Comparator.isDeepEquals(1, 1)).toBe(true)
        expect(Comparator.isDeepEquals(1, "1")).toBe(false)
        expect(Comparator.isDeepEquals(1, 2)).toBe(false)
        expect(Comparator.isDeepEquals(null, undefined)).toBe(false)
    });

    it('check isArraysEquals', () => {
        expect(Comparator.isArraysEquals([1,2,3], [1,2,3])).toBe(true)
        expect(Comparator.isArraysEquals([1,2,3], [1,"2",3])).toBe(false)
        expect(Comparator.isArraysEquals([1,"2",3], [1,"2",3])).toBe(true)
    });

    it('check isObjectsEquals', () => {
        expect(Comparator.isObjectsEquals({a: "test", b: "test"}, {a: "test", b: "test"})).toBe(true)
        expect(Comparator.isObjectsEquals({a: "test", b: "test", c: "1"}, {a: "test", b: "test", c: "1"})).toBe(true)
        expect(Comparator.isObjectsEquals({a: "test", b: "test", c: "1"}, {a: "test", b: "test", c: 1})).toBe(false)
        expect(Comparator.isObjectsEquals({b: "test", c: "1"}, {a: "test", b: "test", c: 1})).toBe(false)
        expect(Comparator.isObjectsEquals({a: "test", b: "tests"}, {a: "test", b: "test"})).toBe(false)
    });

    it('check complexity', () => {
        let a = {
            var1: "asdasd",
            var2: {
                1: {
                    arr: ["test", "test2", "test3", "test4", "test5", "test", "test2", "test3", "test4", "test5"]
                },
                2: {
                    arr2: [{test1: 456},"test", "test2", "test3", "test4", "test5", "test", "test2", "test3", "test4", "test5", {
                        test6: [
                            1, 2, {
                                test: 1531
                            }
                        ]
                    }]
                }
            },
            var3: {
                l1: [
                    {val: "asdasd"}
                ]
            },
        }
        let b = {
            var1: "asdasd",
            var2: {
                1: {
                    arr: ["test", "test2", "test3", "test4", "test5", "test", "test2", "test3", "test4", "test5"]
                },
                2: {
                    arr2: [{test1: 456},"test", "test2", "test3", "test4", "test5", "test", "test2", "test3", "test4", "test5", {
                        test6: [
                            1, 2, {
                                test: 1531
                            }
                        ]
                    }]
                }
            },
            var3: {
                l1: [
                    {val: "asdasd"}
                ]
            },
        }
        let c = {
            var1: "asdasd",
            var2: {
                1: {
                    arr: ["test", "test2", "test3", "test4", "test5", "test", "test2", "test3", "test4", "test5"]
                },
                2: {
                    arr2: [{test1: 456},"test", "test2", "test3", "test4", "test5", "test", "test2", "test3", "test4", "test5", {
                        test6: [
                            1, 2, {
                                test: 1531
                            }
                        ]
                    }]
                }
            },
            var3: {
                l1: [
                    {val: "asdasd"}
                ]
            },
        }
        console.time("complexity")
        let result = Comparator.isDeepEquals(a, b);
        console.timeEnd("complexity")

        expect(result).toBe(true)

        expect(a.var2[2].arr2[11]["test6"][2]["test"]).toBe(1531)
        c.var2[2].arr2[11]["test6"][2]["test"] = 1532

        expect(Comparator.isDeepEquals(a, c)).toBe(false)
    });
});

