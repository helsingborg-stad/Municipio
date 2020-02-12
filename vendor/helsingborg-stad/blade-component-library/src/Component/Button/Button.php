<?php

namespace BladeComponentLibrary\Component\Button;

class Button extends \BladeComponentLibrary\Component\BaseController
{

    public function init()
    {
        //Extract array for eazy access (fetch only)
        extract($this->data);

		$this->data['id'] = uniqid("", true);

		$typeClass = '__' . $type;
		$colorClass = '__' . $type . '--' . $color;
	
		$this->addToClassList(true, $typeClass, $colorClass);
		$this->setSize($text, $icon, $size);

		if($toggle) $this->setToggleAttributes();
		if($ripple) $this->setRipple();
		if($reversePositions) $this->reversePositions();
	}
	
	/**
	 * Set attributes
	 *
	 * @return void
	 */
    private function setToggleAttributes()
    {
		$toggleId = uniqid('', true);
		
		if(!array_key_exists('js-toggle-trigger',$this->data['attributeList'])){
			$this->data['attributeList']['js-toggle-trigger'] = $toggleId;
			$this->data['attributeList']['js-toggle-item'] = $toggleId;
		}
		
		$this->addToClassList(true, '__toggle');
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
		foreach($classList as $class){
			if($prependBaseClass) $class = $this->getBaseClass() . $class;

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
	private function setSize($text, $icon, $size)
	{
		$class = (!$text && $icon) ? '__icon-size--' . $size : '--' . $size;

		$this->addToClassList(true, $class);
	}

	/**
	 * Set ripple animation on click
	 *
	 * @return void
	 */
	private function setRipple()
    {
		$this->addToClassList(false, 'ripple', 'ripple--before');
	}
	
	/**
	 * Reverse the positions of text and icon
	 *
	 * @return void
	 */ 
	private function reversePositions()
	{
		$this->data['labelMod'] = '--reverse';	
	}
}
