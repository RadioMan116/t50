import { mount, shallow } from 'enzyme';
import { h } from 'preact';
import Checkbox from "../src/Checkbox"
import { AssertionError } from 'assert';

describe('checkbox', () => {
    it('should has id increment', () => {
        const wrapper = shallow(<Checkbox text="текст" />)
        expect(wrapper.state("checked")).toBe(false);
        expect(wrapper.find('input').prop("checked")).toBe(false);
        expect(wrapper.find('#checkbox_1')).toHaveLength(1);
        expect(wrapper.find('#checkbox_2')).toHaveLength(0);

        const wrapper2 = shallow(<Checkbox text="текст" checked={true} />)
        expect(wrapper2.state("checked")).toBe(true);
        expect(wrapper2.find('input').prop("checked")).toBe(true);
        expect(wrapper2.find('#checkbox_2')).toHaveLength(1);
        expect(wrapper2.exists("checkbox_2"))
        expect(wrapper2.contains(<label for="checkbox_2" class="check-elem__label" />)).toBe(true)
    });

    it('should display text', () => {
        const wrapper = shallow(<Checkbox text="текст" checked={false} />)
        expect(wrapper.text()).toContain('текст');
        const wrapper2 = shallow(<Checkbox checked={false} />)
        expect(wrapper2.text()).toBe("")
    });

    it('test callback', () => {
        let callbackExpected = true;
        let callback = (result: boolean) => {
            expect(result).toBe(callbackExpected)
        }
        const checkbox = shallow(<Checkbox text="текст" checked={false} onClick={callback}/>)
        checkbox.find("input").simulate("click")
        expect(checkbox.state('checked')).toBe(true);

        callbackExpected = false
        checkbox.find("input").simulate("click")
        expect(checkbox.state('checked')).toBe(callbackExpected);

        callbackExpected = true
        checkbox.setProps({checked: callbackExpected})
        expect(checkbox.state('checked')).toBe(callbackExpected);
    });

    it('test confirm callback', () => {
        let callbackExpected1 = true;
        let callbackWithoutAnswer = (result: boolean) => {
            expect(result).toBe(callbackExpected1)
        }
        const checkbox1 = shallow(<Checkbox text="текст" checked={false} onClick={callbackWithoutAnswer} awaitConfirm={true}/>)
        checkbox1.find("input").simulate("click")
        expect(checkbox1.state('checked')).toBe(false);

        let callbackExpected2 = true;
        let callbackWithFalse = (result: boolean) => {
            expect(result).toBe(callbackExpected2)
            return false
        }
        const checkbox2 = shallow(<Checkbox text="текст" checked={false} onClick={callbackWithFalse} awaitConfirm={true}/>)
        checkbox2.find("input").simulate("click")
        expect(checkbox2.state('checked')).toBe(false);

        let callbackExpected3 = true;
        let callbackWithTrue = (result: boolean) => {
            expect(result).toBe(callbackExpected3)
            return true
        }
        const checkbox3 = shallow(<Checkbox text="текст" checked={false} onClick={callbackWithTrue} awaitConfirm={true}/>)
        checkbox3.find("input").simulate("click")
        expect(checkbox3.state('checked')).toBe(true);
    });
});