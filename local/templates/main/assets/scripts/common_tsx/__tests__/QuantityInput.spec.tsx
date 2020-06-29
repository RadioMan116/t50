import { mount, shallow } from 'enzyme';
import { h } from 'preact';
import QuantityInput from "../src/QuantityInput"
import { AssertionError } from 'assert';

describe('quantity input', () => {
    it('check insert props', () => {
        const wrapper = shallow(<QuantityInput value={"5" as any} />)
        expect(wrapper.state("value")).toBe(5);
        expect(wrapper.find('input').prop("value")).toBe(5);
    });

    let expectAfterClickButton = (wrapper: any, buttonIndex: 0|1, expected: number) => {
        let buttons = wrapper.find("button")
        buttons.at(buttonIndex).simulate("click")
        expect(wrapper.find('input').prop("value")).toBe(expected);
    }

    let expectAfterKeyDown = (wrapper: any, keyCode: string, expected: number) => {
        wrapper.find('input').simulate('keydown', { code: keyCode});
        expect(wrapper.find('input').prop("value")).toBe(expected);
    }

    it('check events buttons', () => {
        const wrapper = shallow(<QuantityInput value={2} />)
        expectAfterClickButton(wrapper, 0, 1)
        expectAfterClickButton(wrapper, 0, 0)
        expectAfterClickButton(wrapper, 0, 0)
        const wrapper2 = shallow(<QuantityInput value={2} min={1} max={4}/>)
        expectAfterClickButton(wrapper2, 0, 1)
        expectAfterClickButton(wrapper2, 0, 1)
        expectAfterClickButton(wrapper2, 1, 2)
        expectAfterClickButton(wrapper2, 1, 3)
        expectAfterClickButton(wrapper2, 1, 4)
        expectAfterClickButton(wrapper2, 1, 4)
    })

    it('check key arrows', () => {
        const wrapper = shallow(<QuantityInput value={2} />)
        expectAfterKeyDown(wrapper,"s", 2)
        expectAfterKeyDown(wrapper, "ArrowUp", 3)
        const wrapper2 = shallow(<QuantityInput value={2} min={-1}/>)
        expectAfterKeyDown(wrapper2, "ArrowDown", 1)
        expectAfterKeyDown(wrapper2, "ArrowDown", 0)
        expectAfterKeyDown(wrapper2, "ArrowDown", -1)
        expectAfterKeyDown(wrapper2, "ArrowDown", -1)
        expectAfterKeyDown(wrapper2, "ArrowUp", 0)
        expectAfterKeyDown(wrapper2, "ArrowUp", 1)
    })

    // it('check manual change', () => {
    //     const wrapper = shallow(<QuantityInput value={2} min={1} />)
    //     // wrapper.find('input').simulate('input', { target: {value: 's'}})
    //     wrapper.find('input').simulate('change', {target: {value: 'foo', name: 'name'}})
    //     // expect(wrapper.find('input').prop("value")).toBe(3);
    //     console.log(wrapper.find('input').debug());
    //     // wrapper.find('input').simulate('change', { value: -1})
    //     // expect(wrapper.find('input').prop("value")).toBe(3);
    // })

    it('check callback', () => {
        const callback = jest.fn()
        const wrapper = shallow(<QuantityInput value={2} onChange={callback}/>)
        expectAfterKeyDown(wrapper, "ArrowUp", 3)
        expect(callback).toHaveBeenCalledWith(3)
    })
});