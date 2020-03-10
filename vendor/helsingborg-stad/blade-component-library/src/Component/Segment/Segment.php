<?php

namespace BladeComponentLibrary\Component\Segment;

class Segment extends \BladeComponentLibrary\Component\BaseController
{

    public function init() {

        //Extract array for eazy access (fetch only)
        extract($this->data);

        if (!empty($layout)) {
            $this->data['classList'][] = $this->getBaseClass() . "__layout--" . $layout;
        }

        if (!empty($background_image)) {
            $this->data['attributeList']['style'] = "background-image: url('".$background_image."');";
        }

        if (!empty($height)) {
            $this->data['classList'][] = $this->getBaseClass() . "__height--" . $height;
        }

        if (!empty($color)) {
            $this->data['classList'][] = $this->getBaseClass() . "__color--" . $color;
        }

        if (!empty($overlay) ) {
            $this->data['classList'][] = $this->getBaseClass() . "__overlay--" . $overlay;
        }

        if (!empty($overlay_opacity)) {
            $this->data['classList'][] = $this->getBaseClass() . "__overlay--opacity-" .  $overlay_opacity;
        }
    }
}