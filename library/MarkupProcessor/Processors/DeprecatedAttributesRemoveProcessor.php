<?php

namespace Municipio\MarkupProcessor\Processors;

use Municipio\MarkupProcessor\MarkupProcessorInterface;

/**
 * Class DeprecatedAttributesRemoveProcessor
 *
 * This processor is responsible for removing deprecated attributes from the markup.
 * It extends the MarkupProcessor class and implements the process method to perform the removal.
 */
class DeprecatedAttributesRemoveProcessor implements MarkupProcessorInterface
{
    public function process(string $markup): string
    {
        return $this->dropPropertyAttributes([
            'style' => ['type' => 'text/css'],
            'script' => ['type' => 'text/javascript'],
        ], $markup);
    }

    private function dropPropertyAttributes(array $dropAttributesConfig, string $cleanedHtml): string
    {
        foreach ($dropAttributesConfig as $tag => $attributes) {
            foreach ($attributes as $attribute => $value) {
                // Create the pattern to match the attribute with the specified value
                $pattern = '/<' . $tag . '\s+([^>]*\s*)' . $attribute . '=["\']' . preg_quote($value, '/') . '["\']([^>]*)>/is';

                // Replace the matched tag by removing the specified attribute
                $cleanedHtml = preg_replace_callback(
                    $pattern,
                    function ($matches) use ($tag) {
                        // Rebuild the tag without the specified attribute
                        return '<' . $tag . ' ' . $matches[1] . $matches[2] . '>';
                    },
                    $cleanedHtml,
                );
            }
        }

        return $cleanedHtml;
    }
}
