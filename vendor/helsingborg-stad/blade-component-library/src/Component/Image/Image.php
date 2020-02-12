<?php

namespace BladeComponentLibrary\Component\Image;

class Image extends \BladeComponentLibrary\Component\BaseController
{

	public function init()
	{

		//Extract array for eazy access (fetch only)
		extract($this->data);

		//Add placeholder class
		if (!$src) {
			$this->data['classList'][] = $this->getBaseClass() . "--is-placeholder";
		}

		//Make full width
		if ($fullWidth) {
			$this->data['classList'][] = $this->getBaseClass() . "--full-width";
		}

		//Inherit the alt text
		if (!$alt && $caption) {
			$this->data['alt'] = $this->data['caption'];
		}

		//Rounded corners all sides
		if ($rounded) {
			$this->data['classList'][] = $this->getBaseClass() . "--rounded ";
		}

		//Rounded corners top left
		if ($roundedTopLeft) {
			$this->data['classList'][] = $this->getBaseClass() . "--rounded-top-left ";
		}

		//Rounded corners top right
		if ($roundedTopRight) {
			$this->data['classList'][] = $this->getBaseClass() . "--rounded-top-right ";
		}

		//Rounded corners bottom left
		if ($roundedBottomLeft) {
			$this->data['classList'][] = $this->getBaseClass() . "--rounded-bottom-left ";
		}

		//Rounded corners bottom right
		if ($roundedBottomRight) {
			$this->data['classList'][] = $this->getBaseClass() . "--rounded-bottom-right ";
		}

		//Rounded corners radius
		if ($roundedRadius) {
			$this->data['classList'][] = $this->getBaseClass() . "--rounded-".$roundedRadius;
		}
	}
}