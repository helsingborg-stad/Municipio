<?php

namespace BladeComponentLibrary\Component\Slider;

class Slider extends \BladeComponentLibrary\Component\BaseController
{

    public function init()
    {
        //Extract array for eazy access (fetch only)
        extract($this->data);

        $this->data['id'] = uniqid("", true);
        $this->data['attributeList']['data-step'] = 0;
        $this->data['attributeList']['js-slider'] = 0;
        $this->data['attributeList']['js-slider-index'] = 0;
    }
}
