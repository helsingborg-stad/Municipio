<?php

namespace Municipio\MarkupProcessor\Processors;

use Municipio\MarkupProcessor\MarkupProcessorInterface;

/**
 * Escape bare ampersands in HTML text after Tidy and markup filters have run.
 */
class EscapeRawAmpersandsProcessor implements MarkupProcessorInterface
{
    public function process(string $markup): string
    {
        // Leave tags, comments, scripts and styles untouched. Tidy already escapes
        // attribute values, and ampersands in scripts and styles are not HTML text.
        return preg_replace_callback(
            '~<!--.*?-->|<(script|style)\b(?:"[^"]*"|\'[^\']*\'|[^\'">])*?>.*?</\1\s*>|<(?:"[^"]*"|\'[^\']*\'|[^\'">])*?>|([^<]+)~is',
            fn(array $matches): string => isset($matches[2])
                ? $this->escapeText($matches[2])
                : $matches[0],
            $markup,
        ) ?? $markup;
    }

    private function escapeText(string $text): string
    {
        return preg_replace_callback(
            '/&(?:\#(?:[0-9]+|[xX][0-9A-Fa-f]+);|[A-Za-z][A-Za-z0-9]+;)?/',
            static function (array $matches): string {
                $entity = $matches[0];

                return $entity !== '&'
                    && html_entity_decode($entity, ENT_QUOTES | ENT_HTML5, 'UTF-8') !== $entity
                        ? $entity
                        : '&amp;' . substr($entity, 1);
            },
            $text,
        ) ?? $text;
    }
}
