<?php

namespace Municipio\PostTypeDesign;

class InlineCssGenerator 
{
    public function __construct(private array $designConfig, private array $fields)
    {}
    
    public function generate(): array
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

    private function isValidColorField($field, $designConfigField, $output)
    {
        return 
            empty($output['choice']) || 
            empty($designConfigField[$output['choice']]) || 
            empty($output['property']);
    }
}