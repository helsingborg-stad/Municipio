<?php

namespace BladeComponentLibrary\Component\Calendar;

class Calendar extends \BladeComponentLibrary\Component\BaseController
{

    public function init()
    {
        //Extract array for eazy access (fetch only)
        extract($this->data);
		$attributes = [
			'eventsUrl' => $eventsUrl,
			'bookingUrl' =>  $bookingUrl,
			'weekStart' => $weekStart,
			'size' => $size,
			'color' => $color,
			'js-toggle-class' => 'ad'
		];

		$this->addToAttributeList($attributes);
		$this->setColor($color);
		$this->data['id'] = uniqid("", true);
		$this->data['toggleId'] = uniqid("", true);
	}

	private function addToAttributeList($attributeList)
    {
		foreach($attributeList as $key => $value){
			$this->data['attributeList'][$key] = $value;	
		}
	} 

	private function setColor($color){
		$this->data['classList'][] = $this->getBaseClass() . '--' . $color;
	}
}
