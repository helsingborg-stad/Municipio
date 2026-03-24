<?php

namespace Municipio\MarkupProcessor\Processors;

use Municipio\MarkupProcessor\MarkupProcessorInterface;

/**
 * Class ScriptMinifyProcessor
 *
 * This processor is responsible for minifying JavaScript content within <script> tags in the markup.
 * It extends the MarkupProcessor class and implements the process method to perform the minification.
 */
class ScriptMinifyProcessor implements MarkupProcessorInterface
{
    public function process(string $markup): string
    {
        if (defined('MUNIPIO_DISABLE_JS_MINIFY') && constant('MUNIPIO_DISABLE_JS_MINIFY') === true) {
            return $markup;
        }

        return preg_replace_callback(
            '/<script\b([^>]*)>(.*?)<\/script>/is',
            fn($m) => '<script' . $m[1] . '>' . $this->minifyJs($m[2]) . '</script>',
            $markup,
        );
    }

    private function minifyJs(string $js): string
    {
        $js = preg_replace('/^[ \t]*\/\/.*$/m', '', $js); // Remove single line comments
        return preg_replace(['/\/\*.*?\*\//s', '/\s+/'], ['', ' '], trim($js)); // Minify
    }
}
