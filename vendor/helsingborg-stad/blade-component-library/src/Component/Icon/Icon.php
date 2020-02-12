<?php

namespace BladeComponentLibrary\Component\Icon;

/**
 * Class Icon
 * @package BladeComponentLibrary\Component\Icon
 */
class Icon extends \BladeComponentLibrary\Component\BaseController {

    public function init() {
        //Extract array for easy access (fetch only)
        extract($this->data);

        // Make data accessible
        $this->compParams = [
            'label'     => $label,
            'color'     => $color,
            'size'      => $size
        ];
        
        $this->setColor();
        $this->appendSpace();
        $this->setSize();
    }

    /**
	 * Appends space before label
	 * @return array
	 */
    public function appendSpace() {
        if($this->compParams['label'] = trim($this->compParams['label'])) {
            $this->data['label'] = " " . $this->compParams['label'];
        }

        return $this->data;
    }

    /**
	 * Build class for color
	 * @return array
	 */
    public function setColor() {
        // Set color based on provided name
        if(isset($this->compParams['color'])) {
            $this->data['classList'][] =
                $this->getBaseClass()."--color-". strtolower($this->compParams['color']);
        }

        return $this->data;
    }


    /**
	 * Build class for size
	 * @return array
	 */
    public function setSize() {
        //Available sizes
        $sizes = [
            'xs' => '16',
            'sm' => '24',
            'md' => '32',
            'lg' => '48',
            'xl' => '64',
            'xxl' => '80',
        ];

        //Size class
        if(isset($sizes[$this->compParams['size']])) {
            $this->data['classList'][] = $this->getBaseClass()."--size-".$this->compParams['size'];
        } else {
            $this->data['classList'][] = $this->getBaseClass() . "--size-inherit";
        }

        return $this->data;
    }
}
