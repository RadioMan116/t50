<?php

namespace Agregator\Components\Traits;

use Agregator\Components\InputManager;


trait ComponentData
{
    function val($field, $default = ""){
        $code = \T50Text::camelCase($field, false);
        $value = $this->input->$code;
        return $value ?? $default;
    }

    function getErrors(){
        return $this->validator->getErrors();
    }

    protected function prepare(array $prepareRules, array $data = array()){
        $inputManager = new InputManager();
        $inputManager->setMapPrepare($prepareRules);
        if( !empty($data) )
            $inputManager->setData($data);
        $this->input = new \StdClass;
        foreach($inputManager->getData() as $code => $value)
            $this->input->$code = $value;

        return $this;
    }

    protected function validate(array $validateRules){
        $this->validator->setData($this->input)->setRules($validateRules);
        return $this->validator->validate();
    }

    protected function updArResult(array $data){
        foreach($data as $code => $value)
            $this->arResult[$code] = $value;
    }
}
