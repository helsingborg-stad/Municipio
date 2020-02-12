<?php

namespace BladeComponentLibrary\Component\Tile;

class Tile extends \BladeComponentLibrary\Component\BaseController
{
    public function init()
    {
		//Extract array for eazy access (fetch only)
        extract($this->data);

        $this->setSize($width, $height);
        $this->setBackgroundImage($backgroundImage);
    }
    
    private function setSize($width, $height)
    {
        $this->data['classList'][] = $this->getBaseClass() . '__item--width' . $width;
        $this->data['classList'][] = $this->getBaseClass() . '__item--height' . $height;
    }

    private function setBackgroundImage($img) {
        if ($img != "")
            $this->data['attributeList']['style'] = 'background-image: url(' . $img . ')';
    }
}