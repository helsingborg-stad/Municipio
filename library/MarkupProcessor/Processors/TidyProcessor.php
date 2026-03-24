<?php

namespace Municipio\MarkupProcessor\Processors;

use Municipio\MarkupProcessor\MarkupProcessorInterface;

/**
 * Class TidyProcessor
 *
 * This processor is responsible for tidying up the HTML markup using the tidy extension.
 * It extends the MarkupProcessor class and implements the process method to perform the tidying.
 */
class TidyProcessor implements MarkupProcessorInterface
{
    public function process(string $markup): string
    {
        if (!class_exists('tidy') || defined('DISABLE_HTML_TIDY') && constant('DISABLE_HTML_TIDY') === true) {
            return $markup;
        }

        $tidy = new \tidy();
        $tidy->parseString(
            $markup,
            [
                'indent' => true,
                'output-xhtml' => false,
                'wrap' => PHP_INT_MAX,
                'doctype' => 'html5',
                'drop-empty-elements' => false,
                'drop-empty-paras' => false,
                'new-pre-tags' => 'template',
            ],
            'utf8',
        );

        // Clean and repair the document
        $tidy->cleanRepair();
        return (string) $tidy;
    }
}
