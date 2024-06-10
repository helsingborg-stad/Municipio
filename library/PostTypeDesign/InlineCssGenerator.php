<?php

namespace Municipio\PostTypeDesign;

class InlineCssGenerator 
{
    private array $acceptedFieldTypes = [];
    private array $extraCssVariables = [];

    /**
     * InlineCssGenerator constructor.
     *
     * @param array $designConfig The design configuration array.
     * @param array $fields The fields array.
     */
    public function __construct(private array $designConfig, private array $fields)
    {
        $this->acceptedFieldTypes = ['color', 'multicolor'];
        
        // These are manually added CSS variables to enhance the inline design.
        $this->extraCssVariables = [
            '--c-nav-v-color-contrasting' => '--color-secondary-contrasting',
            '--c-nav-v-color-contrasting-active' => '--color-secondary-contrasting',
            '--c-nav-item-background-color' => '--color-secondary',
            '--c-nav-v-background-active' => '--color-secondary-dark'
        ];
    }
    
    /**
     * Generate an array of inline CSS based on the design configuration and fields.
     *
     * @return array The generated inline CSS array.
     */
    public function generateCssArray(): array
    {
        $inlineCss = [];
        
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
        
        $inlineCss = array_merge($inlineCss, $this->addExtraCssVariables($inlineCss));

        return $inlineCss;
    }

    private function addExtraCssVariables(array $inlineCss): array
    {
        foreach ($this->extraCssVariables as $variable => $value) {
            if (empty($inlineCss[$value])) {
                continue;
            }

            $inlineCss[$variable] = $inlineCss[$value];
        }

        return $inlineCss;
    }

    /**
     * Generate a string of inline CSS based on the generated CSS array.
     *
     * @return string The generated inline CSS string.
     */
    public function generateCssString(): string
    {
        $inlineCssArray = $this->generateCssArray();
        $cssString = '';

        if (empty($inlineCssArray)) {
            return $cssString;
        }

        foreach ($inlineCssArray as $property => $value) {
            $cssString .= "$property: $value; ";
        }
        
        return $cssString;
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