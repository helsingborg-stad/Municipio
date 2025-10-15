<?php

namespace Modularity\Module\Posts\TemplateController;

/**
 * Class SliderTemplate
 *
 * Template controller for rendering posts as a slider.
 *
 * @package Modularity\Module\Posts\TemplateController
 */
class SliderTemplate extends AbstractController
{
    /**
     * SliderTemplate constructor.
     *
     * @param \Modularity\Module\Posts\Posts $module Instance of the Posts module.
     */
    public function __construct(\Modularity\Module\Posts\Posts $module)
    {
        parent::__construct($module);

        $this->data['slider'] = $this->addSliderViewData();

        $this->data['postsDisplayAs'] = !empty($this->fields['posts_display_as']) ? 
            $this->fields['posts_display_as'] : 'segment';
    }

    private function addSliderViewData() {
        $slider = [];
        $slider['slidesPerPage'] = $this->getSlidesPerPage();
        $slider['autoSlide']     = isset($this->fields['auto_slide']) ? (bool) $this->fields['auto_slide'] : false;
        $slider['showStepper']   = isset($this->fields['show_stepper']) ? (bool) $this->fields['show_stepper'] : false;
        $slider['repeatSlide']   = isset($this->fields['repeat_slide']) ? (bool) $this->fields['repeat_slide'] : true;

        return apply_filters(
            'Modularity/Module/Posts/Slider/Arguments',
            (object) $slider
        );
    }

    private function getSlidesPerPage() {
        return !empty($this->fields['posts_columns']) ?
            12 / (int) str_replace('grid-md-', " ", $this->fields['posts_columns']) : 1;
    }
}
