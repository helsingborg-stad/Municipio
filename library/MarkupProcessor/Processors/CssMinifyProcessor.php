<?php

namespace Municipio\MarkupProcessor\Processors;

use Municipio\MarkupProcessor\MarkupProcessorInterface;

/**
 * Class CssMinifyProcessor
 *
 * This processor is responsible for minifying CSS content within <style> tags in the markup.
 * It extends the MarkupProcessor class and implements the process method to perform the minification.
 */
class CssMinifyProcessor implements MarkupProcessorInterface
{
    public function process(string $markup): string
    {
        if (defined('MUNIPIO_DISABLE_CSS_MINIFY') && constant('MUNIPIO_DISABLE_CSS_MINIFY') === true) {
            return $markup;
        }

        return preg_replace_callback(
            '/<style(?:\s+[^>]*)?>(.*?)<\/style>/is',
            fn($m) => '<style>' . $this->minifyCss($m[1]) . '</style>',
            $markup,
        );
    }

    private function minifyCss(string $css): string
    {
        return preg_replace(['/\/\*.*?\*\//s', '/\s+/'], ['', ' '], trim($css));
    }
}
