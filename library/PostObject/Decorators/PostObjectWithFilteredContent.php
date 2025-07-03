<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\PostObjectInterface;
use WpService\WpService;

/**
 * Decorator for applying WordPress content and title filters to post objects.
 */
class PostObjectWithFilteredContent extends AbstractPostObjectDecorator implements PostObjectInterface
{
    private ?string $filteredContent = null;
    private ?string $filteredTitle   = null;

    /**
     * Constructor.
     */
    public function __construct(
        PostObjectInterface $postObject,
        private WpService $wpService
    ) {
        parent::__construct($postObject);
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        if ($this->filteredContent === null) {
            $this->filteredContent = $this->getFilteredContent();
        }

        return $this->filteredContent;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        if ($this->filteredTitle === null) {
            $this->filteredTitle = $this->wpService->applyFilters(
                'the_title',
                $this->postObject->getTitle(),
                $this->getId()
            );
        }

        return $this->filteredTitle;
    }

    /**
     * Get filtered content by applying WordPress content filters.
     *
     * This method replicates the logic from Helper\Post::getFilteredContent()
     * but works with the PostObject system.
     */
    private function getFilteredContent(): string
    {
        $rawContent = $this->postObject->getContent();

        // Parse lead using <!--more--> tag
        $parts = explode("<!--more-->", $rawContent);

        if (is_array($parts) && count($parts) > 1) {
            // Remove the now broken more block
            foreach ($parts as &$part) {
                $part = str_replace('<!-- wp:more -->', '', $part);
                $part = str_replace('<!-- /wp:more -->', '', $part);
            }

            $excerpt = $this->removeEmptyPTag(array_shift($parts));
            $excerpt = $this->createLeadElement($excerpt);
            $excerpt = $this->replaceBuiltinClasses($excerpt);
            $excerpt = $this->handleBlocksInExcerpt($excerpt);

            $content = $this->replaceBuiltinClasses($this->removeEmptyPTag(implode(PHP_EOL, $parts)));
        } else {
            $excerpt = "";
            $content = $this->replaceBuiltinClasses($this->removeEmptyPTag($rawContent));
        }

        // Apply WordPress filters
        $excerpt = $this->wpService->applyFilters('the_excerpt', $excerpt);
        $content = $this->wpService->applyFilters('the_content', $content);

        // Build filtered content
        return $excerpt . $content;
    }

    /**
     * Add lead class to first excerpt p-tag
     *
     * @param string $lead      The lead string
     * @param string $search    What to search for
     * @param string $replace   What to replace with
     * @return string           The new lead string
     */
    private function createLeadElement($lead, $search = '<p>', $replace = '<p class="lead">'): string
    {
        if (str_contains($lead, '<img')) {
            $lead = \Municipio\Content\Images::normalizeImages($lead);
        }
        $pos = strpos($lead, $search);

        if ($pos !== false) {
            $lead = substr_replace($lead, $replace, $pos, strlen($search));
        } elseif ($pos === false && $lead === strip_tags($lead)) {
            $lead = $replace . $lead . '</p>';
        }

        return $this->removeEmptyPTag($lead);
    }

    /**
     * Remove empty ptags from string
     *
     * @param string $string    A string that may contain empty ptags
     * @return string           A string that not contain empty ptags
     */
    private function removeEmptyPTag($string): string
    {
        return preg_replace("/<p[^>]*>(?:\s|&nbsp;)*<\/p>/", '', $string);
    }

    /**
     * Handle blocks in excerpt.
     * If the excerpt contains blocks, the blocks are rendered and returned.
     * Otherwise, the excerpt is returned as is.
     *
     * @param string $excerpt The post excerpt.
     * @return string The excerpt with blocks rendered.
     */
    private function handleBlocksInExcerpt(string $excerpt): string
    {
        if (!preg_match('/<!--\s?wp:acf\/[a-zA-Z0-9_-]+/', $excerpt)) {
            return $excerpt;
        }

        return $this->wpService->applyFilters('the_content', $excerpt);
    }

    /**
     * Replace builtin classes with theme-specific classes.
     *
     * @param string $content The content to process.
     * @return string The content with replaced classes.
     */
    private function replaceBuiltinClasses(string $content): string
    {
        return str_replace(
            [
                'wp-caption',
                'c-image-text',
                'wp-image-',
                'alignleft',
                'alignright',
                'alignnone',
                'aligncenter',

                //Old inline transition button
                'btn-theme-first',
                'btn-theme-second',
                'btn-theme-third',
                'btn-theme-fourth',
                'btn-theme-fifth',

                //Gutenberg block image
                'wp-block-image',
                'wp-element-caption',
                '<figcaption>'
            ],
            [
                'c-image',
                'c-image__caption',
                'c-image__image wp-image-',
                'u-float--left@sm u-float--left@md u-float--left@lg u-float--left@xl u-float--left@xl u-margin__y--2 u-margin__right--2@sm u-margin__right--2@md u-margin__right--2@lg u-margin__right--2@xl u-width--100@xs',
                'u-float--right@sm u-float--right@md u-float--right@lg u-float--right@xl u-float--right@xl u-margin__y--2 u-margin__left--2@sm u-margin__left--2@md u-margin__left--2@lg u-margin__left--2@xl u-width--100@xs',
                '',
                'u-margin__x--auto u-text-align--center',

                //Old inline transition button
                'c-button c-button__filled c-button__filled--primary c-button--md',
                'c-button c-button__filled c-button__filled--secondary c-button--md',
                'c-button c-button__filled c-button__filled--secondary c-button--md',
                'c-button c-button__filled c-button__filled--secondary c-button--md',
                'c-button c-button__filled c-button__filled--secondary c-button--md',

                //Gutenberg block image
                'c-image',
                'c-image__caption',
                '<figcaption class="c-image__caption">'
            ],
            $content
        );
    }
}
