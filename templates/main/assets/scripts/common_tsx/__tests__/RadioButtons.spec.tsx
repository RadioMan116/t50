import { mount, shallow } from 'enzyme';
import { h } from 'preact';
import RadioButtons from "../src/RadioButtons"
import { AssertionError } from 'assert';
import { shallowToJson } from 'enzyme-to-json';

describe('RadioButtons', () => {
    it('check default has not checked', () => {
        let data = [
            {value: "Y", title: "Да"},
            {value: "N", title: "Нет"},
        ]
        const wrapper = shallow(<RadioButtons onChange={null} data={data} value="" itemClass="table__item" name="test"/>)
        let radio = wrapper.find("input");
        expect(radio.at(0).props().checked).toBe(false);
        expect(radio.at(1).props().checked).toBe(false);
    });

    it('has checked', () => {
        let data = [
            {value: "Y", title: "Да"},
            {value: "N", title: "Нет"},
        ]
        const wrapper = shallow(<RadioButtons onChange={null} data={data} value="N" itemClass="table__item" name="test"/>)
        expect(wrapper.state().value).toBe("N");
        let radio = wrapper.find("input");
        expect(radio.at(0).props().checked).toBe(false);
        expect(radio.at(1).props().checked).toBe(true);
        expect(shallowToJson(wrapper)).toMatchSnapshot();
    });

    it('test callback', () => {
        const onChange = (value: string) => {
            expect(value).toBe("N");
        };
        let data = [
            {value: "Y", title: "Да"},
            {value: "N", title: "Нет"},
        ];
        const wrapper = shallow(<RadioButtons onChange={onChange} data={data} value="Y" itemClass="table__item" name="test"/>)

        expect(wrapper.state().value).toBe("Y");
        wrapper.setState({value: "N"})
        wrapper.setState({value: "Y"})
        let radio = wrapper.find("input");
        expect(radio.at(0).props().checked).toBe(true);
        expect(radio.at(1).props().checked).toBe(false);

        radio.at(1).simulate("click")
        expect(wrapper.state().value).toBe("N");

        wrapper.setState({value: "Y"})
        wrapper.setState({value: "N"})
        radio = wrapper.find("input");
        expect(radio.at(0).props().checked).toBe(false);
        expect(radio.at(1).props().checked).toBe(true);
    });

    it('change props', () => {
        let data = [
            {value: "Y", title: "Да"},
            {value: "N", title: "Нет"},
        ]
        const wrapper = shallow(<RadioButtons onChange={null} data={data} value="N" itemClass="table__item" name="test"/>)
        // console.log(wrapper.debug());
        expect(wrapper.state().value).toBe("N");
        let radio = wrapper.find("input");

        expect(radio.at(0).prop("checked")).toBe(false);
        expect(radio.at(1).prop("checked")).toBe(true);

        wrapper.setProps({ value: 'Y' });
        expect(wrapper.state().value).toBe("Y");
        // console.log(wrapper.debug());
        radio = wrapper.find("input");
        expect(radio.at(0).prop("checked")).toBe(true);
        expect(radio.at(1).prop("checked")).toBe(false);
    });

});