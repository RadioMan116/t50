import { mount, shallow } from 'enzyme';
import { h } from 'preact';
// import { AssertionError } from 'assert';
// import { shallowToJson } from 'enzyme-to-json';
import {Suppliers} from '@Conteiners/Suppliers';

describe('RadioButtons', () => {
    it('check default has not checked', () => {
        let items = [
            {id: 1, title: "title_1"},
            {id: 2, title: "title_2"},
            {id: 3, title: "title_3"},
            {id: 4, title: "title_4"},
            {id: 5, title: "title_5"},
        ]
        let rrcIds = [3, 5]
        const wrapper = shallow(<Suppliers items={items} rrcIds={rrcIds}/>)
        let checkbox = wrapper.find("Checkbox");

        expect(checkbox.at(0).props().checked).toBe(false);
        expect(checkbox.at(1).props().checked).toBe(false);
        expect(checkbox.at(2).props().checked).toBe(true);
        expect(checkbox.at(3).props().checked).toBe(false);
        expect(checkbox.at(4).props().checked).toBe(true);
    });
});