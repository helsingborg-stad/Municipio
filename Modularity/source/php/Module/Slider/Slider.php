<?php

namespace Modularity\Module\Slider;

use Modularity\Integrations\Component\ImageResolver;
use Modularity\Integrations\Component\ImageFocusResolver;
use ComponentLibrary\Integrations\Image\Image as ImageComponentContract;
class Slider extends \Modularity\Module
{
    public $slug = 'slider';
    public $supports = array();

    public $imageSizes = array(
        'ratio-16-9' => array(1528, false),
        'ratio-10-3' => array(1528, false),
        'ratio-36-7' => array(1528, false),
        'ratio-4-3'  => array(1528, false)
    );

    public $paddingRatios = array(
        'ratio-16-9' => 56.25,
        'ratio-10-3' => 30,
        'ratio-36-7' => 19.44,
        'ratio-4-3'  => 75
    );

    public function init()
    {
        $this->nameSingular = __("Slider", 'modularity');
        $this->namePlural = __("Sliders", 'modularity');
        $this->description = __("Outputs multiple images or videos in a sliding apperance.", 'modularity');

        //Adds backwards compability to when we didn't have focal points
        add_filter('acf/load_value/key=field_56a5ed2f398dc', array($this,'filterDesktopImage'), 10, 3);
    }

    /**
     * Adds backwards compability to sliders created before focal point support. 
     *
     * @param array $field
     * @return array $field
     */
    public function filterDesktopImage($value, $postId, $field) {

        if(!is_array($value) && is_numeric($value) && $field['type'] == "focuspoint") {
            return [
                'id' => $value,
                'top' => "40",
                'left' => "50"
            ]; 
        }

        return $value; 
    }

    public function data() : array
    {
        //Get settings
        $fields = $this->getFields();

        $data = [];

        //Assign settings to objects
        $data['autoslide']  = $fields['slides_autoslide'] ? intval($fields['slides_slide_timeout']) : false;
        $data['ratio']      = preg_replace('/ratio-/', '', $fields['slider_format'] ?? 'ratio-16-9');
        $data['wrapAround'] = in_array('wrapAround', $fields['additional_options'] ?? []);
        $data['title'] = isset($fields['post_title']) ? $fields['post_title'] : '';
        $data['slidesPerPage'] = isset($fields['slides_per_page']) ? $fields['slides_per_page'] : '1';
        $data['ariaLabels'] =  (object) [
            'prev' => __('Previous slide','modularity'),
            'next' => __('Next slide', 'modularity'),
            'first' => __('Go to first slide', 'modularity'),
            'last' => __('Go to last slide','modularity'),
            'slideX' => __('Go to slide %s', 'modularity'),
        ];

        $imageSize = isset($this->imageSizes[$fields['slider_format']]) ? 
            $this->imageSizes[$fields['slider_format']] : 
            [1800, 350];

        //Get slides
        if (isset($fields['slides']) && is_array($fields['slides'])) {
            $data['slides'] = array_map(function($slide) use ($imageSize) {
                return $this->prepareSlide($slide, $imageSize);
            }, $fields['slides']);
        }

        $data['id'] = $this->ID;

        //Translations
        $data['lang'] = (object) [
            'noSlidesHeading' => __('Slider is empty','modularity'),
            'noSlides' => __('Please add something to slide.','modularity')
        ]; 

        return $data;
    }

    /**
     * Prepare slide
     * 
     * @param array $slide
     * @param array $imageSize
     * 
     * @return array
     */
    private function prepareSlide($slide, array $imageSize) {
        $slide = $slide['acf_fc_layout'] === 'video' ? 
            $this->prepareVideoSlide($slide, $imageSize) : 
            $this->prepareImageSlide($slide, $imageSize); 

        $slide = $this->getLinkData($slide);

        return $slide;
    }

    /**
     * Prepare image slide
     * 
     * @param array $slide
     * @param array $imageSize
     * 
     * @return array
     */
    private function prepareImageSlide(array $slide, array $imageSize) {
        //If no image, return slide
        if (!isset($slide['image']['id'])) {
            return $slide;
        }

        //Try to get image contract
        $imageContract = $this->getImageContract(
            $slide['image']['id'], 
            $slide['image'] ?? null
        );

        //If we have a contract, use it, else fallback to normal image
        if($imageContract) {
            $slide['image'] = $imageContract;
            $slide['hasImageContract'] = true;
        } else {
            $slide['image'] = \Municipio\Helper\Image::getImageAttachmentData(
                $slide['image']['id'] ?? null,
                $imageSize
            );
            $slide['hasImageContract'] = false;
        }

        return $slide;
    }    
    
    /**
     * Prepare video slide
     * 
     * @param array $slide
     * @param array $imageSize
     * 
     * @return array
     */
    private function prepareVideoSlide(array $slide, array $imageSize) {
        //Try to get image contract
        $imageContract = $this->getImageContract(
            $slide['image'], 
            null
        );

        //If we have a contract, use it, else fallback to normal image
        if($imageContract) {
            $slide['image'] = $imageContract;
            $slide['hasImageContract'] = true;
        } else {
            $slide['image'] = \Municipio\Helper\Image::getImageAttachmentData(
                $slide['image']['id'] ?? null,
                $imageSize
            );
            $slide['hasImageContract'] = false;
        }
        
        return $slide;
    }

    /**
     * Get image contract
     * 
     * @param int $imageId
     * @param array $focus
     * @return ImageComponentContract|null
     */
    private function getImageContract(int $imageId, ?array $focus = null): ?ImageComponentContract {

        return ImageComponentContract::factory(
            (int) $imageId,
            [1920, false],
            new ImageResolver(),
            !is_null($focus) ? new ImageFocusResolver($focus) : new ImageFocusResolver(['id' => $imageId])
        ); 
    }

    /**
     * Get link data
     * 
     * @param array $slide
     * @return array
     */
    private function getLinkData(array $slide) {

        if(!$this->slideHasLink($slide)) {
            return $slide;
        }

        if ($this->isValidLinkUrl($slide)) {
            $slide['link_url'] = get_permalink($slide['link_url']);
        }

        // Set link text
        if (empty($slide['link_text'])) {
            $slide['link_text'] = __('Read more', 'modularity');
        }

        $slide['call_to_action'] = false;
        if ($this->isButtonCta($slide)) {
            $slide['call_to_action'] = array(
                'title' => $slide['link_text'],
                'href' => $slide['link_url']
            );
            //remove link url, instead use CTA
            $slide['link_url'] = false;
        }

        return $slide;
    }

    /**
     * Check if slide has link
     * 
     * @param array $slide
     * @return bool
     */
    public function slideHasLink(array $slide):bool {
        if( $slide['link_type'] !== 'internal' && $slide['link_type'] !== 'external' ) {
            return false;
        }

        if( empty($slide['link_url']) ) {
            return false;
        }

        return true;
    }

    /**
     * Check if link url is valid
     * 
     * @param array $slide
     * @return bool
     */
    private function isValidLinkUrl($slide) {
        return 
            isset($slide['link_url']) && 
            is_numeric($slide['link_url']) && 
            get_post_status($slide['link_url']) == "publish";
    }

    /**
     * Check if slide is a button CTA
     * 
     * @param array $slide
     * @return bool
     */
    private function isButtonCta($slide) {
        return 
            !empty($slide['link_type']) && 
            $slide['link_type'] !== 'false' && 
            $slide['link_style'] === 'button';
    }

    /**
     * Available "magic" methods for modules:
     * init()            What to do on initialization
     * data()            Use to send data to view (return array)
     * style()           Enqueue style only when module is used on page
     * script            Enqueue script only when module is used on page
     * adminEnqueue()    Enqueue scripts for the module edit/add page in admin
     * template()        Return the view template (blade) the module should use when displayed
     */
}
