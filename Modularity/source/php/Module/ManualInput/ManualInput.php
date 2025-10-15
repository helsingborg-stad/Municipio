<?php

namespace Modularity\Module\ManualInput;

use Municipio\Helper\Image as ImageHelper;

use Modularity\Integrations\Component\ImageResolver;
use Modularity\Integrations\Component\ImageFocusResolver;
use ComponentLibrary\Integrations\Image\Image as ImageComponentContract;
use Modularity\Module\ManualInput\Private\PrivateController;

class ManualInput extends \Modularity\Module
{
    public $slug = 'manualinput';
    public $supports = array();
    public $blockSupports = array(
        'align' => ['full']
    );

    public string $postStatus;
    public $template;

    private ?PrivateController $privateController = null;

    public function init()
    {
        $this->nameSingular = __("Manual Input", 'modularity');
        $this->namePlural = __("Manual Inputs", 'modularity');
        $this->description = __("Creates manual input content.", 'modularity');

        $this->privateController = new PrivateController($this);

        add_filter('Modularity/Block/Data', array($this, 'blockData'), 50, 3);
    }

    public function data(): array
    {
        $data           = [];
        $fields         = $this->getFields();
        $displayAs      = $this->getTemplateToUse($fields);
        $this->template = $displayAs;

        $data['manualInputs']      = [];
        $data['ID']                = !empty($this->ID) ? $this->ID : uniqid('manualinput-');
        $data['columns']           = !empty($fields['columns']) ? $fields['columns'] . '@md' : 'o-grid-4@md';
        $data['context']           = ['module.manual-input.' . $this->template];
        $data['ratio']             = !empty($fields['ratio']) ? $fields['ratio'] : '4:3';
        $data['imagePosition']     = !empty($fields['image_position']) ? true : false;
        $imageSize                 = $this->getImageSize($displayAs);
        $data['freeTextFiltering'] = !empty($fields['free_text_filtering']) ? true : false;
        $data['lang']              = [
            'search' => __('Search', 'modularity'),
        ];

        $data['accordionColumnTitles'] = $this->createAccordionTitles(
            isset($fields['accordion_column_titles']) ? $fields['accordion_column_titles'] : [], 
            isset($fields['accordion_column_marking']) ? $fields['accordion_column_marking'] : ''
        );

        // Accordion settings
        if ($fields['display_as'] === 'accordion') {
            $data['accordionSpacedSections'] = $fields['accordion_spaced_sections'] ?? false;
        }

        // Card settings
        if ($fields['display_as'] === 'card') {
            $data['titleAboveImage'] = $fields['title_above_image'] ?? false;
            $data['disableLayoutShift'] = $fields['disable_resize_layout_shift'] ?? false;
        }

        if (!empty($fields['manual_inputs']) && is_array($fields['manual_inputs'])) {
            foreach ($fields['manual_inputs'] as $index => &$input) {
                $input = array_filter($input, function($value) {
                    return !empty($value) || $value === false;
                });

                // Custom background color
                $customBackgroundColor = ($fields['use_custom_card_color'] ?? false) 
                                            && !empty($input['custom_background_color']) 
                                            && strpos($input['custom_background_color'], '::') !== false
                    ? explode('::', $input['custom_background_color'])[0]
                    : false;
                
                // Custom text color
                $customTextColor = $customBackgroundColor 
                    ? \Municipio\Helper\Color::getBestContrastColor(explode('::', $input['custom_background_color'])[1], false)
                    : false;

                // Extra classes
                if ($customBackgroundColor) {
                    $input['classList'] = ['c-card--has-custom-background'];
                }

                $arr                            = array_merge($this->getManualInputDefaultValues(), $input);
                $arr['isHighlighted']           = $this->canBeHighlighted($fields, $index);
                $arr['id']                      = 'item-' . $data['ID'] . '-' . $index;
                // TODO: change name and migrate
                $arr['icon']                    = $arr['box_icon'];
                $arr['image']                   = $this->maybeGetImageImageContract($displayAs, $arr['image']) ?? $this->getImageData($arr['image'], $imageSize);
                $arr['accordion_column_values'] = $this->createAccordionTitles($arr['accordion_column_values'], $arr['title']);
                $arr['view']                    = $this->getInputView($arr['isHighlighted']);
                $arr['columnSize']              = $this->getInputColumnSize($fields, $arr['isHighlighted']);
                $arr['attributeList']           = ['id' => $arr['id']];
                $arr['custom_background_color'] = $customBackgroundColor;
                $arr['custom_text_color']       = $customTextColor;
                $arr                            = \Municipio\Helper\FormatObject::camelCase($arr);

                $data['manualInputs'][]         = (array) $arr;
            }
        }

        //Check if any item has an image
        $data['anyItemHasImage'] = array_reduce($data['manualInputs'], function($carry, $item) {
            if (isset($item['image'])) {
                if (is_a($item['image'], ImageComponentContract::class)) {
                    return $carry || $item['image']->getUrl();
                }
                return $carry || !empty($item['image']);
            }
            return $carry;
        }, false);

        $data = $this->privateController->decorateData($data, $fields);
        return $data;
    }

    private function maybeGetImageImageContract(string $displayAs, int $imageId) {

        $width = $this->getImageSize($displayAs, 'width');

        if (in_array($displayAs, ['segment', 'block', 'card'])) {
            return ImageComponentContract::factory(
                $imageId,
                [$width, false],
                new ImageResolver(),
                new ImageFocusResolver(['id' => $imageId])
            );
        }

        return null;
    }

    /**
     * @return array Array with default values
     */
    private function getManualInputDefaultValues(): array
    {
        return [
            'title'                     => null,
            'content'                   => null,
            'link'                      => null,
            'link_text'                 => null,
            'default_link_text'         => __('Read more', 'modularity'),
            'image'                     => null,
            'accordion_column_values'   => [],
            'box_icon'                  => null,
            'custom_background_color'   => null,
        ];
    }

    /**
     * Returns the input view based on the given fields and index.
     *
     * @param array $fields The array of fields.
     * @param int $index The index of the field.
     * @return string The input view.
     */
    private function getInputView(bool $shouldBeHighlighted): string
    {
        return $shouldBeHighlighted ? $this->getHighlightedView() : $this->template;
    }

    /**
     * Returns the input column size based on the given fields and index.
     *
     * @param array $fields The array of fields.
     * @param int $index The index of the field.
     * @return string The input column size.
     */
    private function getInputColumnSize(array $fields, bool $shouldBeHighlighted): string
    {
        $columnSize = !empty($fields['columns']) ? $fields['columns'] : 'o-grid-4';

        if ($shouldBeHighlighted) {
            return $this->getHighlightedColumnSize($columnSize) . '@md';
        }
        
        return $columnSize . '@md';
    }

    /**
     * Determines if the input field can be highlighted.
     *
     * @param array $fields The array of input fields.
     * @param int $index The index of the current input field.
     * @return bool Returns true if the input field can be highlighted, false otherwise.
     */
    private function canBeHighlighted(array $fields, int $index) 
    {
        return $index === 0 && !empty($fields['highlight_first_input']) && in_array($this->template, ['card', 'block', 'segment']);
    }

    /**
     * Gets the highlighted column size based on the given column size.
     *
     * @param string $columnSize The column size.
     * @return string The highlighted column size.
     */
    private function getHighlightedColumnSize(string $columnSize): string
    {
        switch ($columnSize) {
            case 'o-grid-6':
                return 'o-grid-12';
            case 'o-grid-4':
                return 'o-grid-8';
            case 'o-grid-3':
                return 'o-grid-6';
            default:
                return $columnSize;
        }
    }

    /**
     * Returns the highlighted view based on the template property.
     *
     * @return string The highlighted view.
     */
    private function getHighlightedView(): string 
    {
        switch ($this->template) {
            case "segment":
                return "segment";
            case "block":
                return "card";
            case "card":
                return "block";
            default:
                return $this->template;
        }
    }

    /**
     * Get all data attached to the image.
     * 
     * @param array $fields All the acf fields
     * @param array|string $size Array containing height and width OR predefined size as a string.
     * @return array
     */
    private function getImageData($imageId = false, $size = [400, 225])
    {
        if (!empty($imageId)) {
            $image = ImageHelper::getImageAttachmentData($imageId, $size);

            if ($image) {
                $image['removeCaption'] = true;
            }

            unset($image['title']);
            unset($image['description']);

            return $image;
        }

        return false;
    }

    /**
     * Decides the size of the image based on view
     * 
     * @param string $displayAs The name of the template/view.
     * @return array
     */
    private function getImageSize($displayAs, $return = "both"): null|array|int {
        switch ($displayAs) {
            case "segment": 
                $dimensions =  [1920, 1080];
            case "block":
                $dimensions =  [1024, 1024];
            case "collection": 
            case "box":
                $dimensions =  [768, 768];
            default: 
                $dimensions = [1440, 810];
        }

        if($return == "width") {
            return $dimensions[0] ?? null;
        }

        if($return == "height") {
            return $dimensions[1] ?? null;
        }

        return $dimensions;
    }

     /**
     * @param array $accordionColumnTitles Array of arrays
     * @param string $accordionColumnMarker
     * @return array
     */
    private function createAccordionTitles($accordionColumnTitles = false, $accordionColumnMarker = false) {
        $titles = [];
        if (!empty($accordionColumnTitles) || !empty($accordionColumnMarker)) {
            if (!empty($accordionColumnMarker)) {
                $titles[] = is_string($accordionColumnMarker) ? $accordionColumnMarker : __('Title', 'Modularity');
            }

            if (!empty($accordionColumnTitles) && is_array($accordionColumnTitles)) {
                foreach ($accordionColumnTitles as $accordionColumnTitle) {
                    $titles = array_merge($titles, array_values($accordionColumnTitle));
                }
            }
        }

        return $titles;
    }

    /**
     * Add full width setting to frontend.
     * @param [array] $viewData
     * @param [array] $block
     * @param [object] $module
     * @return array
     */
    public function blockData($viewData, $block, $module) {
        if (strpos($block['name'], "acf/manualinput") === 0 && $block['align'] == 'full' && !is_admin()) {
            $viewData['stretch'] = true;
        } else {
            $viewData['stretch'] = false;
        }
        return $viewData;
    }

    /**
     * Determine the template to use for rendering based on field configuration.
     *
     * This function calculates the template name to use for rendering based on the
     * provided field configuration. If the 'display_as' key is specified in the
     * configuration and is not empty, it will be used as the template name. Otherwise,
     * the default template name 'card' will be used. The calculated template name is
     * passed through a filter 'Modularity/Module/ManualInput/Template' to allow
     * customization.
     *
     * @param array $fields The field configuration array.
     * @return string The template name to use for rendering.
     */
    public function getTemplateToUse($fields) {
        $templateName = !empty($fields['display_as']) ? $fields['display_as'] : 'card'; 
        return apply_filters(
            'Modularity/Module/ManualInput/Template', 
            $templateName 
        );
    }

    /**
     * Get the template file name for rendering.
     *
     * This function returns the name of the template file to use for rendering
     * based on the template property of the current object. If the specified
     * template file exists, it will be returned; otherwise, a default template
     * ('card.blade.php') will be used.
     *
     * @return string The template file name.
     */
    public function template() {
        $path = __DIR__ . "/views/" . $this->template . ".blade.php";

        if (file_exists($path)) {
            return $this->template . ".blade.php";
        }
        
        return 'base.blade.php';
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
