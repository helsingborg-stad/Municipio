<?php

namespace BladeComponentLibrary\Component\Segment;

class Segment extends \BladeComponentLibrary\Component\BaseController
{

    public function init() {

        //Extract array for eazy access (fetch only)
        extract($this->data);

        //Full template
        if($template == "full") {
            $this->getFullTemplateData($parallax); 
        }

        if($template == "split") {
            $this->getSplitTemplateData($reverse_layout); 
        }

        if($template == "featured") {
            $this->getFeaturedTemplateData($reverse_layout);
        }

        if($template == "card") {
            $this->getCardTemplateData($background_color); 
        }

        $this->getTemplateClass($template);
        $this->getHeight($height);
        $this->getPadding($padding);
        $this->getContainment($contain_content);
        $this->getImageFocus($image_focus);
        $this->getContentAlignment($content_alignment);
        $this->getMobileLayout($mobile_layout);
        $this->getTextAlignment($text_alignment);
        $this->getBackground($background_color, $background_image);
        $this->getCtaAlignment($cta_align);
    }

    private function getTemplateClass ($template) {
        $this->data['classList'][] = $this->getBaseClass() . "--template-" . $template;
    }

    /**
     * Template specific
     *
     * @param Boolean $parallax
     * @return void
     */
    private function getFullTemplateData($parallax) {
        $this->data['classList'][] = $this->getBaseClass() . "--padding-md";
        $this->data['classList'][] = $this->getBaseClass() . "--valign-middle";
        $this->data['classList'][] = $this->getBaseClass() . "--overlay-dark";
        $this->data['classList'][] = $this->getBaseClass() . "--color-light";
        $this->data['classList'][] = $this->getBaseClass() . "--overlay-opacity-medium";

        $parallax ? 
            $this->data['classList'][] = $this->getBaseClass() . "--effect-parallax" :
            '';
    }

    /**
     * Template Specific
     *
     * @param String $reverse_layout
     * @return void
     */
    private function getSplitTemplateData($reverse_layout) {
        $this->getOrder($reverse_layout);
        $this->data['classList'][] = $this->getBaseClass() . "--valign-middle";
    }

    /**
     * Template specific
     *
     * @return void
     */
    private function getCardTemplateData() {
        $this->data['classList'][] = $this->getBaseClass() . "--valign-middle";
    }

    /**
     * Template specific 
     *
     * @param String $reverse_layout
     * @return void
     */
    private function getFeaturedTemplateData($reverse_layout) {
        $this->getOrder($reverse_layout);
    }

    /**
     * Build class for padding
     *
     * @param [type] $padding
     * @return void
     */
    private function getPadding($padding) {
        $this->data['classList'][] = $this->getBaseClass() . "__padding--" . $padding;
    }

    /**
     * Creates class for the alignment of the cta
     *
     * @param String $align
     * @return void
     */
    private function getCtaAlignment($align) {
        $this->data['classList'][] = $this->getBaseClass() . "__cta--" . $align;
    }

    /**
     * Sets the background
     *
     * @param String $color What color to apply
     * @param String $image An URL to the image to use as background (optional)
     * @return void
     */
    private function getBackground($color, $image) {
        $image != "" ?
            $this->data['attributeList']['style'] = "background-image: url('".$image."');" :
                $this->data['classList'][] = $this->getBaseClass() . "__background--" . $color;
    }

    /**
     * Sets height of segment
     *
     * @param String $height
     * @return void
     */
    private function getHeight($height) {
        $this->data['classList'][] = $this->getBaseClass() . "--height-" . $height;
    }

    /**
     * If segment should be contained within the container
     *
     * @param Boolean $contain_content
     * @return void
     */
    private function getContainment($contain_content) {
        $contain_content ? '' :
            $this->data['classList'][] = $this->getBaseClass() . "--not-contained";
    }

    /**
     * Orders the main and secondary content to get desired layout
     *
     * @param Boolean $reverse_layout
     * @return void
     */
    private function getOrder($reverse_layout) {
        $reverse_layout ?
            $this->data['classList'][] = $this->getBaseClass() . "--reverse-layout"
            : '';
    }

    /**
     * Where on the image to focus the layout on
     *
     * @param Array $image_focus
     * @return void
     */
    public function getImageFocus($image_focus) {
        $this->data['classList'][] =
            $this->getBaseClass() .
                "--image-focus-{$image_focus['horizontal']}-{$image_focus['vertical']}";
    }

    /**
     * Where to place secondary content relative to main content in mobile
     *
     * @param Array $mobile_layout Options for mobile layout
     * @return void
     */
    public function getMobileLayout($mobile_layout) {
        $this->data['classList'][] =
            $this->getBaseClass() . "--graphics-" . $mobile_layout['graphics'];
    }
    
    /**
     * Handles the text alignment option
     *
     * @param String $text_alignment
     * @return void
     */
    public function getTextAlignment($text_alignment) {
        $this->data['classList'][] =
            $this->getBaseClass() . "--text-alignment-" . $text_alignment;
    }

    /**
     * Handles the alignment options for content
     *
     * @param Array $content_alignment The array that holds the alignment data
     * @return void
     */
    public function getContentAlignment($content_alignment) {
        // Align vertical
        $v_align = [
            'center'    => '--valign-center',
            'top'       => '--valign-top',
            'bottom'    => '--valign-bottom'
        ];

        $this->data['classList'][] =
            $this->getBaseClass() . $v_align[$content_alignment['vertical']];

        // Align horizontal
        $h_align = [
            'center'    => '--align-center',
            'left'      => '--align-left',
            'right'     => '--align-right',
        ];

        $this->data['classList'][] =
            $this->getBaseClass() . $h_align[$content_alignment['horizontal']];
    }
}