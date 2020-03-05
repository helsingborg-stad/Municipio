<?php

namespace BladeComponentLibrary\Component\Card;

/**
 * Class Card
 * @package BladeComponentLibrary\Component\Card
 */
class Card extends \BladeComponentLibrary\Component\BaseController
{

	public function init()
	{
		//Extract array for eazy access (fetch only)
		extract($this->data);

		$this->data['classList'][] = $baseClass . '--' . $color; 

		if(isset($image['padded']) && $image['padded']){
			$this->data['paddedImage'] = $this->getBaseClass() . '__image-background--padded'; 	
		} 
		if($imageFirst){
			$this->data['classList'][] = $this->getBaseClass() . '--image-first'; 
		} 

		if($collapsible){
			$this->data['collapsible'] = 'c-card--collapse';
		} 
		

	}



}
