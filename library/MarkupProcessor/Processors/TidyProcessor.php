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

        [$markup, $templates] = $this->extractOuterTemplates($markup) ?? ['', []];
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
                'new-blocklevel-tags' => 'design-builder,range-controller',
            ],
            'utf8',
        );

        // Clean and repair the document
        $tidy->cleanRepair();
        $markup = (string) $tidy;
        $markup = str_replace(array_keys($templates), array_values($templates), $markup);
        return $markup;
    }

    /**
     * Extract outermost <template> tags and replace them with placeholders
     *
     * @param string $html The HTML content
     *
     * @return array An array containing the HTML with <template> tags replaced by placeholders and the extracted templates
     */
    private function extractOuterTemplates(string $html): array
    {
        $templates = [];

        // Match all <template> and </template> tags with offsets
        $pattern = '#</?template\b[^>]*>#i';
        preg_match_all($pattern, $html, $matches, PREG_OFFSET_CAPTURE);

        $stack = [];
        $outermost = []; // will hold start/end positions of outer templates

        foreach ($matches[0] as $match) {
            $tag = strtolower($match[0]);
            $pos = $match[1];

            if ($tag === '<template>' || strpos($tag, '<template ') === 0) {
                if (empty($stack)) {
                    $outermost[] = ['start' => $pos, 'end' => null];
                }
                $stack[] = $pos;
            } else {
                array_pop($stack);
                if (empty($stack)) {
                    $outermost[count($outermost) - 1]['end'] = $pos + strlen($match[0]);
                }
            }
        }

        // Replace templates with placeholders in reverse order (so offsets remain valid)
        for ($i = count($outermost) - 1; $i >= 0; $i--) {
            $start = $outermost[$i]['start'];
            $end = $outermost[$i]['end'];

            if ($end === null) {
                continue;
            }

            $full = substr($html, $start, $end - $start);
            $key = '___MUN_TPL_' . count($templates) . '___';
            $templates[$key] = $full;

            // Replace in HTML
            $html = substr_replace($html, $key, $start, $end - $start);
        }

        return [$html, $templates];
    }
}
