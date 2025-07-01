<?php

namespace Municipio\Toc\Utils;

use Municipio\PostObject\PostObjectInterface;

interface TocUtilsInterface
{
    /**
     * Check if TOC should be enabled for the given post.
     *
     * @param PostObjectInterface $postObject The post object to check.
     * @return bool True if TOC should be enabled, false otherwise.
     */
    public function shouldEnableToc(PostObjectInterface $postObject): bool;

    /**
     * Get the table of contents data for the given post content.
     *
     * @param string $content The post content to analyze.
     * @return array The table of contents data.
     */
    public function getTableOfContents(string $content): array;

    /**
     * Get the post content with anchor IDs injected into headings.
     *
     * @param string $content The original post content.
     * @return string The content with anchor IDs added to headings.
     */
    public function getContentWithAnchors(string $content): string;
}