<?php

namespace Municipio\MarkupProcessor\Processors;

use Municipio\MarkupProcessor\MarkupProcessorInterface;

/**
 * Combine adjacent style elements containing only the same CSS layer.
 */
class MergeAdjacentLayerStylesProcessor implements MarkupProcessorInterface
{
    public function process(string $markup): string
    {
        preg_match_all('/<style\b[^>]*>.*?<\/style\s*>/is', $markup, $matches, PREG_OFFSET_CAPTURE);

        $result = '';
        $cursor = 0;
        $pendingStyle = null;
        $pendingLayer = null;

        foreach ($matches[0] as [$style, $start]) {
            $between = substr($markup, $cursor, $start - $cursor);
            $layer = $this->singleLayer($style);

            if ($pendingStyle !== null && $pendingLayer !== null && $layer !== null
                && $pendingLayer[0] === $layer[0] && trim($between) === '') {
                $pendingLayer[1] .= "\n" . $layer[1];
                $pendingStyle = '<style>@layer ' . $layer[0] . ' {' . $pendingLayer[1] . '}</style>';
            } else {
                $result .= ($pendingStyle === null ? '' : $this->reduceImageContainerRules($pendingStyle)) . $between;
                $pendingStyle = $style;
                $pendingLayer = $layer;
            }

            $cursor = $start + strlen($style);
        }

        return $result . ($pendingStyle === null ? '' : $this->reduceImageContainerRules($pendingStyle)) . substr($markup, $cursor);
    }

    /**
     * Identical image declarations can share a container rule. Their generated
     * item classes are distinct, so grouping selectors does not change which
     * image is displayed at each breakpoint.
     */
    private function reduceImageContainerRules(string $style): string
    {
        $layer = $this->singleLayer($style);
        if ($layer === null || $layer[0] !== 'components') {
            return $style;
        }

        $body = $layer[1];
        $offset = 0;
        $groups = [];
        $ruleCount = 0;
        $pattern = '/\G\s*@container\s+([^{}]+?)\s*\{\s*(\.c-image\.c-image--container-query\s+\.c-image--item-[a-z0-9_-]+)\s*\{\s*display\s*:\s*block\s*;\s*\}\s*\}\s*/i';

        while ($offset < strlen($body)) {
            if (!preg_match($pattern, $body, $match, 0, $offset)) {
                return $style;
            }

            $condition = trim($match[1]);
            $groups[$condition][$match[2]] = true;
            $ruleCount++;
            $offset += strlen($match[0]);
        }

        if ($ruleCount === count($groups)) {
            return $style;
        }

        $rules = [];
        foreach ($groups as $condition => $selectors) {
            $rules[] = '@container ' . $condition . ' { '
                . implode(', ', array_keys($selectors)) . ' {display: block;} }';
        }

        return '<style>@layer components {' . implode(' ', $rules) . '}</style>';
    }

    private function singleLayer(string $style): ?array
    {
        if (!preg_match('/^<style>\s*@layer\s+([a-z][\w-]*)\s*\{/i', $style, $match)) {
            return null;
        }

        $closingTag = strripos($style, '</style>');
        if ($closingTag === false) {
            return null;
        }

        $openingBrace = strlen($match[0]) - 1;
        $depth = 0;
        $quote = null;
        $escaped = false;

        for ($i = $openingBrace; $i < $closingTag; $i++) {
            $char = $style[$i];
            if ($quote !== null) {
                if ($escaped) {
                    $escaped = false;
                } elseif ($char === '\\') {
                    $escaped = true;
                } elseif ($char === $quote) {
                    $quote = null;
                }
                continue;
            }

            if ($char === '"' || $char === "'") {
                $quote = $char;
            } elseif ($char === '{') {
                $depth++;
            } elseif ($char === '}') {
                $depth--;
                if ($depth === 0) {
                    return trim(substr($style, $i + 1, $closingTag - $i - 1)) === ''
                        ? [$match[1], substr($style, $openingBrace + 1, $i - $openingBrace - 1)]
                        : null;
                }
            }
        }

        return null;
    }
}
