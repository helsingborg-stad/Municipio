<?php

namespace BladeComponentLibrary\Component\Gallery;

class Gallery extends \BladeComponentLibrary\Component\BaseController
{
	public function init() {
		//Extract array for eazy access (fetch only)
		extract($this->data);
	}

	public static function getUnique(){
		return uniqid();
	}
}
