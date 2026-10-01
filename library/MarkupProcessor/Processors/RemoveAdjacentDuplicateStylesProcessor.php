<?php

namespace Municipio\MarkupProcessor\Processors;

use Municipio\MarkupProcessor\MarkupProcessorInterface;

/**
 * Drop repeated style elements when only whitespace separates them.
 */
class RemoveAdjacentDuplicateStylesProcessor implements MarkupProcessorInterface
{
    public function process(string $markup): string
    {
        preg_match_all('/<style\b[^>]*>.*?<\/style\s*>/is', $markup, $matches, PREG_OFFSET_CAPTURE);

        $result = '';
        $cursor = 0;
        $previousStyle = null;
        $previousEnd = null;

        foreach ($matches[0] as [$style, $start]) {
            $between = substr($markup, $cursor, $start - $cursor);

            if ($previousStyle !== $style || $previousEnd === null || trim(substr($markup, $previousEnd, $start - $previousEnd)) !== '') {
                $result .= $between . $style;
            }

            $previousStyle = $style;
            $previousEnd = $start + strlen($style);
            $cursor = $previousEnd;
        }

        return $result . substr($markup, $cursor);
    }
}
