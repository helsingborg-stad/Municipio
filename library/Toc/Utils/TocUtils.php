<?php

namespace Municipio\Toc\Utils;

use Municipio\PostObject\PostObjectInterface;
use Municipio\Toc\Utils\TableOfContents;
use WpService\WpService;

class TocUtils implements TocUtilsInterface
{
    /**
     * Constructor.
     *
     * @param WpService $wpService The WordPress service instance.
     */
    public function __construct(private WpService $wpService)
    {
    }

    /**
     * @inheritDoc
     */
    public function shouldEnableToc(PostObjectInterface $postObject): bool
    {
        // Enable TOC for single posts/pages with content
        if (!$this->wpService->isSingular()) {
            return false;
        }

        // Check if post has content
        $content = $postObject->getContent();
        if (empty($content)) {
            return false;
        }

        // Check if content has headings
        return $this->hasHeadings($content);
    }

    /**
     * @inheritDoc
     */
    public function getTableOfContents(string $content): array
    {
        if (empty($content)) {
            return [];
        }

        $tableOfContents = new TableOfContents($content);
        return $tableOfContents->getTableOfContents();
    }

    /**
     * @inheritDoc
     */
    public function getContentWithAnchors(string $content): string
    {
        if (empty($content)) {
            return $content;
        }

        $tableOfContents = new TableOfContents($content);
        return $tableOfContents->getDocumentWithAnchors();
    }

    /**
     * Check if the content contains headings (h1-h6).
     *
     * @param string $content The content to check.
     * @return bool True if content has headings, false otherwise.
     */
    private function hasHeadings(string $content): bool
    {
        return preg_match('/<h[1-6][^>]*>.*?<\/h[1-6]>/i', $content) === 1;
    }
}