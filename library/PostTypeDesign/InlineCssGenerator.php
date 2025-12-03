<?php

namespace Municipio\PostTypeDesign;

class InlineCssGenerator 
{
    private array $acceptedFieldTypes = [];

    /**
     * InlineCssGenerator constructor.
     *
     * @param array $designConfig The design configuration array.
     * @param array $fields The fields array.
     */
    public function __construct(private array $designConfig, private array $fields)
    {
        $this->acceptedFieldTypes = ['color', 'multicolor'];
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

            $inlineCss = $this->mergeCssVariables($this->getColorFieldsCss($field, $designConfigField), $inlineCss);
        }


        return $inlineCss;
    }

    /**
     * Merges an array of new CSS variables with an array of old CSS variables.
     *
     * @param array $newCssVariables The array of new CSS variables.
     * @param array $oldCssVariables The array of old CSS variables.
     * @return array The merged array of CSS variables.
     */
    private function mergeCssVariables(array $newCssVariables, array $oldCssVariables): array
    {
        foreach($newCssVariables as $key => $value) {
            if (isset($oldCssVariables[$key])) {
                $oldCssVariables[$key] = array_merge($oldCssVariables[$key], $value);
                continue;
            } else {
                $oldCssVariables[$key] = $value;
            }
        }

        return $oldCssVariables;
    }

    /**
     * Checks if a field is not valid.
     *
     * @param mixed $field The field to check.
     * @return bool Returns true if the field is not valid, false otherwise.
     */
    private function isNotValidField($field): bool
    {
        return 
            empty($this->designConfig[$field['settings']]) || 
            empty($field['output']) || 
            empty($field['type']) ||
            !in_array($field['type'], $this->acceptedFieldTypes);
    }

    /**
     * Generate a string of inline CSS based on the generated CSS array.
     *
     * @param string $cssClassName The base CSS class name.
     * @return string The generated inline CSS string.
     */
    public function generateCssString(string $cssClassName): string
    {
        $inlineCssArray = $this->generateCssArray();
        $cssString = '';

        if (empty($inlineCssArray)) {
            return $cssString;
        }

        foreach ($inlineCssArray as $cssClass => $cssProperties) {
            if (empty($cssProperties)) {
                continue;
            }

            $propertiesString = $this->buildCssPropertiesString($cssProperties);

            // Handle special case for :root
            if ($cssClass === ':root') {
                $cssString .= "{$cssClassName} { {$propertiesString} } ";
                continue;
            }

            // Append regular CSS class rules
            $cssString .= "{$cssClassName} {$cssClass}, " .
                        "{$cssClass} {$cssClassName}, " .
                        "{$cssClassName}{$cssClass} { {$propertiesString} } ";
        }

        return $cssString;
    }

    /**
     * Build a CSS properties string from an associative array.
     *
     * @param array $cssProperties The array of CSS properties.
     * @return string The formatted CSS properties string.
     */
    private function buildCssPropertiesString(array $cssProperties): string
    {
        $properties = [];
        foreach ($cssProperties as $property => $value) {
            $properties[] = "{$property}: {$value};";
        }

        return implode(' ', $properties);
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
        $multiColorKeys = [];

        foreach ($field['output'] as $output) {
            if ($this->isValidColorField($field, $designConfigField, $output)) {
                continue;
            }

            $element = $output['element'];
            $property = $output['property'];
            $choiceValue = $designConfigField[$output['choice']];

            if (!isset($multiColorKeys[$element])) {
                $multiColorKeys[$element] = [];
            }

            $multiColorKeys[$element][$property] = $choiceValue;
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
            empty($output['property']) ||
            empty($output['element']);
    }
}