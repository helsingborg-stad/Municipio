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
        if (!$this->shouldSwitch()) {
            return $this->postObject->getPermalink();
        }

        return $this->getValueFromOtherBlog(fn() => $this->postObject->getPermalink());
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
        if (!$this->shouldSwitch()) {
            return $this->postObject->getIcon();
        }

        return $this->getValueFromOtherBlog(fn() => $this->postObject->getIcon());
    }

    /**
     * @inheritDoc
     */
    public function getBlogId(): int
    {
        return $this->blogId ?? $this->postObject->getBlogId();
    }

    private function getValueFromOtherBlog(callable $callback)
    {
        $this->switch();
        $value = $callback();
        $this->restore();

        return $value;
    }

    /**
     * Determine if we should switch to another blog.
     *
     * @return bool
     */
    private function shouldSwitch(): bool
    {
        return $this->wpService->isMultisite() && $this->getBlogId() !== $this->wpService->getCurrentBlogId();
    }

    private function switch(): void
    {
        $this->wpService->switchToBlog($this->getBlogId());
    }

    private function restore(): void
    {
        $this->wpService->restoreCurrentBlog();
    }
}
