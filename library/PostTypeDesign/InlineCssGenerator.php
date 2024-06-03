<?php

namespace Municipio\PostTypeDesign;

class InlineCssGenerator 
{
    /**
     * InlineCssGenerator constructor.
     *
     * @param array $designConfig The design configuration array.
     * @param array $fields The fields array.
     */
    public function __construct(private array $designConfig, private array $fields)
    {}
    
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
            if (empty($this->designConfig[$field['settings']]) || empty($field['output'])) {
                continue;
            }

            $designConfigField = $this->designConfig[$field['settings']];

            if ($field['type'] === 'multicolor' || $field['type'] === 'color') {
                $inlineCss = array_merge($this->getMultiColorCss($field, $designConfigField), $inlineCss);
            }
        }

        return $inlineCss;
    }

    /**
     * Generate a string of inline CSS based on the generated CSS array.
     *
     * @return string|false The generated inline CSS string, or false if no CSS is generated.
     */
    public function generateCssString(): string|false
    {
        $inlineCssArray = $this->generateCssArray();

        if (empty($inlineCssArray)) {
            return false;
        }

        $cssString = '';
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
    private function getMultiColorCss($field, $designConfigField): array
    {
        $multiColorKeys = [];

        foreach ($field['output'] as $output) {
            if ($this->isValidColorField($field, $designConfigField, $output)) {
                continue;
            }

            $multiColorKeys[$output['property']] = $designConfigField[$output['choice']];
        }

        return $multiColorKeys;
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
}