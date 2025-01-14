<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\Icon\IconInterface;
use Municipio\PostObject\PostObjectInterface;
use WpService\Contracts\GetCurrentBlogId;
use WpService\Contracts\IsMultisite;
use WpService\Contracts\RestoreCurrentBlog;
use WpService\Contracts\SwitchToBlog;

/**
 * Post object decorator that can fetch post data from another blog.
 * If the post is from another blog, it will switch to that blog to fetch the data.
 */
class PostObjectFromOtherBlog implements PostObjectInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        private PostObjectInterface $postObject,
        private IsMultisite&GetCurrentBlogId&SwitchToBlog&RestoreCurrentBlog $wpService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return $this->postObject->getId();
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->postObject->getTitle();
    }

    /**
     * @inheritDoc
     */
    public function getPermalink(): string
    {
        return $this->postObject->getPermalink();
    }

    /**
     * @inheritDoc
     */
    public function getCommentCount(): int
    {
        return $this->postObject->getCommentCount();
    }

    /**
     * @inheritDoc
     */
    public function getPostType(): string
    {
        return $this->postObject->getPostType();
    }

    /**
     * @inheritDoc
     */
    public function getIcon(): ?IconInterface
    {
        if (!$this->wpService->isMultisite()) {
            return $this->postObject->getIcon();
        }

        if ($this->getBlogId() === $this->wpService->getCurrentBlogId()) {
            return $this->postObject->getIcon();
        }

        $this->wpService->switchToBlog($this->getBlogId());
        $icon = $this->postObject->getIcon();
        $this->wpService->restoreCurrentBlog();
        return $icon;
    }

    /**
     * @inheritDoc
     */
    public function getBlogId(): int
    {
        return $this->blogId ?? $this->postObject->getBlogId();
    }
}
