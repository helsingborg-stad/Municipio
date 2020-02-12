<?php

namespace BladeComponentLibrary\Component\Notification;

class Notification extends \BladeComponentLibrary\Component\BaseController  
{
    
    public function init() {

        //Extract array for easy access (fetch only)
        extract($this->data);
        $this->data['classList'][] = $this->getBaseClass() . '__spawn--' . $animation['direction'];
        if(!$animation['onPageLoad']) $this->data['classList'][] = 'u-display--none';
        if($autoHideDuration) $this->data['attributeList']['autoHideDuration'] = $autoHideDuration;
        if($maxAmount) $this->data['attributeList']['maxAmount'] = $maxAmount;
        $this->data['attributeList']['direction'] = $animation['direction'];
    }
}