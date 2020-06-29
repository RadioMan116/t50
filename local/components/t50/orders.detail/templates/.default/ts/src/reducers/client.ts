
import { Action } from "@Root/types";
import { CLIENT_LOAD_DATA, CLIENT_SEND_EMAIL } from "@Root/actions/ClientAction";

export interface IClient
{
    id: number,
    fio: string,
    fio_dop: string,
    phone: string,
    phone_dop: string,
    email: string,
    is_entity: boolean,
    requisites: string,
    city: string,
    street: string,
    house_number: string,
    porch: string,
    floor: string,
    apartment: string,
    intercom: string,
    elevator: string,
    send_email: boolean,
}

const initialState: IClient = {
    id: 0,
    fio: "",
    fio_dop: "",
    phone: "",
    phone_dop: "",
    email: "",
    is_entity: false,
    requisites: "",
    city: "",
    street: "",
    house_number: "",
    porch: "",
    floor: "",
    apartment: "",
    intercom: "",
    elevator: "",
    send_email: false,
};

export default (state = initialState, action: Action) => {
    switch(action.type){
        case CLIENT_LOAD_DATA:
            return action.payload
        case CLIENT_SEND_EMAIL:
            return {...state, send_email: action.payload}
    }
    return state;
}
