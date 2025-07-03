<?php

namespace Municipio\Toc\Utils;

use Municipio\PostObject\PostObjectInterface;
use Municipio\Toc\Utils\TableOfContents;
use WpService\WpService;
use AcfService\AcfService;

/**
 * Class TocUtils
 *
 * Provides utility functions for handling Table of Contents (TOC) features in WordPress posts.
 */
class TocUtils implements TocUtilsInterface
{
    private const MINIMUM_NUMBER_OF_HEADINGS_TO_ENABLE_FEATURE = 3;

    /**
     * Constructor.
     *
     * @param WpService $wpService The WordPress service instance.
     */
    public function __construct(private WpService $wpService, private AcfService $acfService)
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

        //Check if get field 'toc' is set to true
        $isEnabledOnPost = $this->acfService->getField('post_table_of_contents', $postObject->getId(), false) ?? false;
        if ($isEnabledOnPost === false) {
            return false;
        }

        // Check if content has headings
        return $this->hasHeadings($content, self::MINIMUM_NUMBER_OF_HEADINGS_TO_ENABLE_FEATURE);
    }

    /**
     * @inheritDoc
     */
    public function getTableOfContents(string $content): array
    {
        if (empty($content)) {
            return [];
        }

        $tableOfContents = new TableOfContents($content, $this->wpService);
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

        $tableOfContents = new TableOfContents($content, $this->wpService);
        return $tableOfContents->getDocumentWithAnchors();
    }

    /**
     * Check if the content contains a minimum number of headings (h1-h6).
     *
     * @param string $content The content to check.
     * @param int $minimumNumberOfHeadings Minimum number of headings to consider as "has headings".
     * @return bool True if content has at least the minimum number of headings, false otherwise.
     */
    private function hasHeadings(string $content, int $minimumNumberOfHeadings = 1): bool
    {
        preg_match_all('/<h[2-4][^>]*>.*?<\\/h[2-4]>/i', $content, $matches);
        return count($matches[0]) >= $minimumNumberOfHeadings;
    }
}
