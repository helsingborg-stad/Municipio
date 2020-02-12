<?php

namespace BladeComponentLibrary\Component\Paper;

class Paper extends \BladeComponentLibrary\Component\BaseController 
{
    public function init() {
        //Extract array for eazy access (fetch only)
        extract($this->data);

        $this->compParams = [
            'padding' 	=> $padding ?? false,
            'transparent' 	=> $transparent ?? false
        ];

        $this->setData();
    }

    /**
     * Set data for paper
     */
    public function setData(){

        //Create padding && transparent modifier
        $this->data['classList']['padding'] = (is_numeric($this->compParams['padding'])) ?
            $this->getBaseClass() . "--padding-" . $this->compParams['padding'] : '';

        $this->data['classList']['transparent'] = ($this->compParams['transparent']) ?
            $this->getBaseClass() . "--transparent" : '';

    }

}