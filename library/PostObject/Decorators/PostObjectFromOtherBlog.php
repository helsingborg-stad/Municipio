<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\Icon\IconInterface;
use Municipio\PostObject\PostObjectInterface;
use WpService\Contracts\RestoreCurrentBlog;
use WpService\Contracts\SwitchToBlog;

/**
 * Post object decorator that can fetch post data from another blog.
 * If the post is from another blog, it will switch to that blog to fetch the data.
 */
class PostObjectFromOtherBlog extends AbstractPostObjectDecorator implements PostObjectInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        PostObjectInterface $postObject,
        private SwitchToBlog&RestoreCurrentBlog $wpService,
        private int $blogId
    ) {
        parent::__construct($postObject);
    }

    /**
     * @inheritDoc
     */
    public function getPermalink(): string
    {
        return $this->addBlogIdQueryVarToUrl($this->getValueFromOtherBlog(fn() => $this->postObject->getPermalink()));
    }

    /**
     * Add the blog ID as a query variable to the URL.
     *
     * @param string $url The URL to which the blog ID should be added.
     *
     * @return string The URL with the blog ID query variable appended.
     */
    private function addBlogIdQueryVarToUrl(string $url): string
    {
        $varPrefix = empty(parse_url($url)['query']) ? '?' : '&';

        return $url . $varPrefix . 'blog_id=' . $this->getBlogId();
    }

    /**
     * @inheritDoc
     */
    public function getIcon(): ?IconInterface
    {
        return $this->getValueFromOtherBlog(fn() => $this->postObject->getIcon());
    }

    /**
     * @inheritDoc
     */
    public function getBlogId(): int
    {
        return $this->blogId;
    }

    /**
     * Get the value from another blog.
     */
    private function getValueFromOtherBlog(callable $callback)
    {
        $this->switch();
        $value = $callback();
        $this->restore();

        return $value;
    }

    /**
     * Switch to another blog.
     */
    private function switch(): void
    {
        $this->wpService->switchToBlog($this->getBlogId());
    }

    /**
     * Restore the current blog.
     */
    private function restore(): void
    {
        $this->wpService->restoreCurrentBlog();
    }
}
