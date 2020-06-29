import parameters, { IParameters } from "@Reducers/parameters";
import { CHANGE_CHECK_RRC, CHANGE_PARAMETERS } from "@Root/consts";

describe('reducers parameters', () => {
    it('parameters change state', () => {
        let state: IParameters = {blocks: [{}, {}], check_rrc: false}

        state = parameters(state, {type: CHANGE_CHECK_RRC, payload: true})
        expect(state).toEqual({blocks: [{}, {}], check_rrc: true})

        state = parameters(state, {type: CHANGE_PARAMETERS, payload: [{}, {}, {entry_add_percent:7.5, entry_to: 15}]})
        expect(state).toEqual({blocks: [{}, {}, {entry_add_percent:7.5, entry_to: 15}], check_rrc: true})
    });
});