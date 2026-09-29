<?php

namespace Municipio\MarkupProcessor\Processors;

use Municipio\MarkupProcessor\MarkupProcessorInterface;

/**
 * Restore ampersands that were escaped twice in rendered labels.
 */
class DecodeDoubleEscapedAmpersandsProcessor implements MarkupProcessorInterface
{
    public function process(string $markup): string
    {
        if (!str_contains($markup, '&amp;amp;')) {
            return $markup;
        }

        return preg_replace_callback(
            '~<!--.*?-->|<(script|style)\b(?:"[^"]*"|\'[^\']*\'|[^\'">])*?>.*?</\1\s*>|<(?:(?:"[^"]*")|(?:\'[^\']*\')|[^\'">])*?>|([^<]+)~is',
            function (array $matches): string {
                if (isset($matches[2])) {
                    return str_replace('&amp;amp;', '&amp;', $matches[2]);
                }

                if (!str_starts_with($matches[0], '<') || str_starts_with($matches[0], '<!--')) {
                    return $matches[0];
                }

                return preg_replace_callback(
                    '/(\s(?:aria-label|alt|title|placeholder)\s*=\s*)(["\'])(.*?)\2/is',
                    static fn(array $attribute): string => $attribute[1]
                        . $attribute[2]
                        . str_replace('&amp;amp;', '&amp;', $attribute[3])
                        . $attribute[2],
                    $matches[0],
                ) ?? $matches[0];
            },
            $markup,
        ) ?? $markup;
    }
}
