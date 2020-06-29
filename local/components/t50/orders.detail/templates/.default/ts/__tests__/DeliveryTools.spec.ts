import DeliveryTools from "@Root/tools/DeliveryTools";

describe('DeliveryTools', () => {
    it('check DeliveryTools.getTimeIndex()', () => {
        expect(DeliveryTools.getTimeVal("12 - 15", ["12-15", "15-17"])).toEqual("12-15")
        expect(DeliveryTools.getTimeVal(" 12 - 15 ", ["13-15", "15-17"])).toEqual(null)
        expect(DeliveryTools.getTimeVal("12-15", ["13-15", " 12 - 15 ", "15-17"])).toEqual(" 12 - 15 ")
        expect(DeliveryTools.getTimeVal("15-17", ["13-15", " 12 - 15 ", "15-17"])).toEqual("15-17")
        expect(DeliveryTools.getTimeVal(null, ["13-15", " 12 - 15 ", "15-17"])).toEqual(null)
    });
});

