<?php

namespace Municipio\PostTypeDesign\InlineCssDecorators;

class AddCssFromFieldsAndConfig implements InlineCssDecoratorInterface {
    private array $acceptedFieldTypes = ['color', 'multicolor'];

    public function __construct(private array $designConfig, private array $fields)
    {}

    public function decorate(array $inlineCss): array
    {
        if (empty($this->designConfig) || empty($this->fields)) {
            return $inlineCss;
        }

        foreach ($this->fields as $field) {
            if ($this->isNotValidField($field)) {
                continue;
            }

            $designConfigField = $this->designConfig[$field['settings']];
            $inlineCss = array_merge($this->getColorFieldsCss($field, $designConfigField), $inlineCss);
        }
        
        return $inlineCss;
    }

    /**
     * Get the multi-color CSS properties and values based on the field and design configuration.
     *
     * @param array $field The field array.
     * @param array $designConfigField The design configuration field array.
     * @return array The multi-color CSS properties and values.
     */
    private function getColorFieldsCss($field, $designConfigField): array
    {
        $colorKeys = [];

        foreach ($field['output'] as $output) {
            if ($this->isValidColorField($field, $designConfigField, $output)) {
                continue;
            }

            $colorKeys[$output['property']] = $designConfigField[$output['choice']];
        }

        return $colorKeys;
    }


    /**
     * Check if the color field is valid based on the field, design configuration, and output.
     *
     * @param array $field The field array.
     * @param array $designConfigField The design configuration field array.
     * @param array $output The output array.
     * @return bool True if the color field is valid, false otherwise.
     */
    private function isValidColorField($field, $designConfigField, $output)
    {
        return 
            empty($output['choice']) || 
            empty($designConfigField[$output['choice']]) || 
            empty($output['property']);
    }

    private function isNotValidField($field) 
    {
        return 
            empty($this->designConfig[$field['settings']]) || 
            empty($field['output']) || 
            empty($field['type']) ||
            !in_array($field['type'], $this->acceptedFieldTypes);
    }
}
