<?php

namespace Municipio\PostTypeDesign;

use Municipio\PostTypeDesign\InlineCssDecorators\AddCssFromFieldsAndConfig;
use Municipio\PostTypeDesign\InlineCssDecorators\AddValuesFromExistingValues;

class InlineCss 
{
    private array $extraCssVariables = [];

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
        $inlineCss = (new AddCssFromFieldsAndConfig($this->designConfig, $this->fields))->decorate($inlineCss);
        $inlineCss = (new AddValuesFromExistingValues($this->designConfig, $this->fields))->decorate($inlineCss);

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
}