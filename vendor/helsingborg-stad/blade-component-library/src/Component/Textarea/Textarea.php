<?php

namespace BladeComponentLibrary\Component\Textarea;

/**
 * Class Textarea
 * @package BladeComponentLibrary\Component\Textarea
 */
class Textarea extends \BladeComponentLibrary\Component\BaseController
{
    public function init()
    {
        //Extract array for eazy access (fetch only)
        extract($this->data);
        $this->compParams = [
            'label' => $label ?? '',
            'required' => $required ?? false,
            'invalidMessage' => $invalidMessage ?? '',
            'value' => $value ?? '',
        ];

        $this->setData();
    }

    /**
     * Mapping data
     */
    public function setData()
    {
        $this->data['label'] = $this->compParams['label'];
        $this->data['required'] = $this->compParams['required'];
        $this->data['invalidMessage'] = $this->compParams['invalidMessage'];
        $this->data['value'] = $this->compParams['value'];

    }


}
