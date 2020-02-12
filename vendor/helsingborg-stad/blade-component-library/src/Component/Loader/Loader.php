<?php

namespace BladeComponentLibrary\Component\Loader;

class Loader extends \BladeComponentLibrary\Component\BaseController
{
    public function init()
    {
        //Extract array for eazy access (fetch only)
        extract($this->data);

        $this->data['text'] = ($text) ? $text : '';


        $this->setColor($shape, $color);
        $this->setSize($shape, $size);
    }

    private function setColor($shape, $color)
    {
        $this->addToClassList(true, '__'. $shape . '--color--' . $color);
    }

    /**
     * Add one or more classes to the classlist
     *
     * @param Boolean $prependBaseClass Option to prepend the base class(c-button)
     * @param Variadic ...$classList One or more css classes as strings
     * @return void
     */
    private function addToClassList($prependBaseClass, ...$classList)
    {
        foreach ($classList as $class) {
            if ($prependBaseClass) $class = $this->getBaseClass() . $class;

            $this->data['classList'][] = $class;
        }
    }

    /**
     * Set the size, different class depending on content
     *
     * @param String $text The buttons text
     * @param String $icon The name of the icon
     * @param String $size The size of the button(sm, md, lg)
     * @return void
     */
    private function setSize($shape, $size)
    {
        $this->addToClassList(true, '__' . $shape);
        $this->addToClassList(true, '__' . $shape . '--' . $size);
    }

}
