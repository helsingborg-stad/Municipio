<?php

namespace Municipio\Filters;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;

class More implements Hookable
{
    public function __construct(
        private AddFilter $wpService,
    ) {}

    /**
     * Registers the filter for processing content with the <!--more--> tag.
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter('Municipio\Filters\More', [$this, 'filterMore'], 10, 1);
    }

    /**
     * Filters the content to process the <!--more--> tag. It splits the content at the <!--more--> tag and adds a "lead" class to paragraphs in the excerpt.
     * @param string $content The content to filter.
     * @return string The filtered content with the excerpt and remaining content.
     */
    public function filterMore(string $content): string
    {
        if (!$this->containsMoreTag($content)) {
            return $content;
        }

        [$excerpt, $remaining] = $this->splitAtMoreTag($content);

        $excerpt = $this->addLeadToParagraphs($excerpt);

        return $excerpt . $remaining;
    }

    /**
     * Checks if the content contains the <!--more--> tag.
     * @param string $content The content to check.
     * @return bool True if the content contains the <!--more--> tag, false otherwise.
     */
    private function containsMoreTag(string $content): bool
    {
        return !empty($content) && str_contains($content, '<!--more-->');
    }

    /**
     * Splits the content at the <!--more--> tag into an excerpt and remaining content.
     * @param string $content The content to split.
     * @return array An array containing the excerpt and remaining content.
     */
    private function splitAtMoreTag(string $content): array
    {
        $parts = explode('<!--more-->', $content, 2);

        return [
            $parts[0],
            $parts[1] ?? '',
        ];
    }

    /**
     * Adds the "lead" class to all paragraphs in the given HTML content.
     * @param string $html The HTML content to process.
     * @return string The HTML content with the "lead" class added to paragraphs.
     */
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

    /**
     * Appends the "lead" class to the class attribute of a paragraph tag. If the class attribute does not exist, it creates one.
     * @param string $fullTag The full paragraph tag.
     * @param string $attributes The attributes of the paragraph tag.
     * @return string The modified paragraph tag with the "lead" class.
     */
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
