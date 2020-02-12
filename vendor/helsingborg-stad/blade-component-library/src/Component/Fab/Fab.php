<?php

namespace BladeComponentLibrary\Component\Fab;

/**
 * Class Fab
 * @package BladeComponentLibrary\Component\Fab
 */
class Fab extends \BladeComponentLibrary\Component\BaseController
{

    public function init() {
        //Extract array for easy access (fetch only)
        extract($this->data);

        // Make data accessible
        $this->compParams = [
			'position' 	=> $position,
			'spacing' 	=> $spacing
		];

        // Builds class
        $this->buildFabClass();
    }

    /**
	 * Build class based on position and spacing
	 * @return array
	 */
    public function buildFabClass() {
        $tempPosition = !empty($this->compParams['position'])
            ? $this->compParams['position'] : "bottom-right";

        $tempSpacing = !empty($this->compParams['spacing'])
            ? $this->compParams['spacing'] : "md";

        $this->data['classList'][] = "{$this->getBaseClass()}__{$tempPosition}--{$tempSpacing}";

        return $this->data;
    }
}
