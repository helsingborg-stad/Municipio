<?php

namespace Municipio\Toc\PostObject;

use Municipio\PostObject\Decorators\AbstractPostObjectDecorator;
use Municipio\PostObject\PostObjectInterface;
use Municipio\Toc\Utils\TocUtilsInterface;
use WpService\WpService;

/**
 * Decorator for adding Table of Contents functionality to post objects.
 */
class TocPostObject extends AbstractPostObjectDecorator implements PostObjectInterface
{
    private ?array $tableOfContents = null;
    private ?string $contentWithAnchors = null;

    /**
     * TocPostObject constructor.
     *
     * @param PostObjectInterface $postObject The post object to decorate.
     * @param WpService $wpService The WordPress service instance.
     * @param TocUtilsInterface $tocUtils The TOC utilities instance.
     */
    public function __construct(
        PostObjectInterface $postObject,
        private WpService $wpService,
        private TocUtilsInterface $tocUtils
    ) {
        parent::__construct($postObject);
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        if ($this->contentWithAnchors === null) {
            // Get the filtered content from the decorated post object
            $filteredContent = $this->postObject->getContent();
            $this->contentWithAnchors = $this->tocUtils->getContentWithAnchors($filteredContent);
        }

        return $this->contentWithAnchors;
    }

    /**
     * Get the table of contents for this post.
     *
     * @return array The table of contents data.
     */
    public function getTableOfContents(): array
    {
        if ($this->tableOfContents === null) {
            // Get the filtered content from the decorated post object
            $filteredContent = $this->postObject->getContent();
            $this->tableOfContents = $this->tocUtils->getTableOfContents($filteredContent);
        }

        return $this->tableOfContents;
    }

    /**
     * Check if this post has a table of contents.
     *
     * @return bool True if the post has a table of contents, false otherwise.
     */
    public function hasTableOfContents(): bool
    {
        return !empty($this->getTableOfContents());
    }

    /**
     * Magic method to handle dynamic property access for backwards compatibility.
     *
     * @param string $name The property name.
     * @return mixed The property value.
     */
    public function __get(string $name): mixed
    {
        return match ($name) {
            'tableOfContents' => $this->getTableOfContents(),
            'documentWithAnchors' => $this->getContent(),
            'hasTableOfContents' => $this->hasTableOfContents(),
            'postContentFiltered' => $this->getContent(),
            'post_content_filtered' => $this->getContent(),
            default => parent::__get($name),
        };
    }
}