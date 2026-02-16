<?php

namespace Municipio\Filters;

use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;

class More implements Hookable
{
    public function __construct(
        private WpService $wpService,
    ) {}

    /**
     * Adds hooks for the filter.
     */
    public function addHooks(): void
    {
        add_filter('Municipio\Filters\More', [$this, 'filterMore'], 10, 1);
    }

    public function filterMore(string $content): string
    {
        if (!$this->containsMoreTag($content)) {
            return $content;
        }

        [$excerpt, $remaining] = $this->splitAtMoreTag($content);

        $excerpt = $this->addLeadToParagraphs($excerpt);

        return $excerpt . $remaining;
    }

    private function containsMoreTag(string $content): bool
    {
        return !empty($content) && str_contains($content, '<!--more-->');
    }

    private function splitAtMoreTag(string $content): array
    {
        $parts = explode('<!--more-->', $content, 2);

        return [
            $parts[0],
            $parts[1] ?? '',
        ];
    }

    private function addLeadToParagraphs(string $html): string
    {
        return preg_replace_callback(
            '/<p([^>]*)>/',
            function ($matches) {
                return $this->appendLeadClass($matches[0], $matches[1]);
            },
            $html,
        );
    }

    private function appendLeadClass(string $fullTag, string $attributes): string
    {
        if (preg_match('/class=(["\'])(.*?)\1/', $attributes, $classMatch)) {
            $quote = $classMatch[1];
            $classes = preg_split('/\s+/', trim($classMatch[2]));

            if (!in_array('lead', $classes, true)) {
                $classes[] = 'lead';
            }

            $newClass = 'class=' . $quote . implode(' ', $classes) . $quote;

            return str_replace($classMatch[0], $newClass, $fullTag);
        }

        return '<p class="lead"' . $attributes . '>';
    }
}
