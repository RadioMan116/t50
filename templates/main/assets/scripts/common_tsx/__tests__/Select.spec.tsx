import { mount, shallow } from 'enzyme';
import { h } from 'preact';
import { AssertionError } from 'assert';
import { shallowToJson } from 'enzyme-to-json';
import Select from '../src/Select';

global["$"] = global["jQuery"] = (function(){});

describe('select', () => {
    it('default initialize and click change', () => {
        let data = [{val: "1", title: "test1"}, {val: "2", title: "test2"}, {val: "3", title: "test3"}];
        let def = {val: "0", title: "-"}
        const wrapper = shallow(<Select items={data} default={def} onSelect={null} class="asda1das" value="2" />)
        let options = wrapper.find("option")
        expect(options.at(1).prop("selected")).toBe(false)
        expect(options.at(2).prop("selected")).toBe(true)
        expect(options.at(3).prop("selected")).toBe(false)

        // tslint:disable-block
        wrapper.setState({value: 3})
        options = wrapper.find("option")
        expect(options.at(0).prop("disabled")).toBe(true)
        expect(options.at(1).prop("selected")).toBe(false)
        expect(options.at(2).prop("selected")).toBe(false)
        expect(options.at(3).prop("selected")).toBe(true)
        expect(shallowToJson(wrapper)).toMatchSnapshot();
    });

    it('default initialize not disabled and click change', () => {
        let data = [{val: "1", title: "test1"}, {val: "2", title: "test2"}, {val: "3", title: "test3"}];
        let def = {val: 0, title: "-", disabled: false}
        const wrapper = shallow(<Select items={data} default={def} onSelect={null} class="asda1das" value="2" />)
        let options = wrapper.find("option")
        expect(options.at(0).prop("disabled")).toBe(false)
        expect(options.at(1).prop("selected")).toBe(false)
        expect(options.at(2).prop("selected")).toBe(true)
        expect(options.at(3).prop("selected")).toBe(false)

        // tslint:disable-block
        wrapper.setState({value: 0})
        options = wrapper.find("option")
        expect(options.at(0).prop("selected")).toBe(true)
        expect(options.at(1).prop("selected")).toBe(false)
        expect(options.at(2).prop("selected")).toBe(false)
        expect(options.at(3).prop("selected")).toBe(false)
        expect(shallowToJson(wrapper)).toMatchSnapshot();
    });

    it('select simple props set', () => {
        let data = [{val: "1", title: "test1"}, {val: "2", title: "test2"}, {val: "3", title: "test3"}];
        const wrapper = shallow(<Select items={data} onSelect={null} />)
        expect(shallowToJson(wrapper)).toMatchSnapshot();
    });

});