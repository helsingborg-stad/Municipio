<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\Icon\IconInterface;
use Municipio\PostObject\PostObjectInterface;
use Municipio\Schema\BaseType;
use WpService\Contracts\GetSiteUrl;
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
        private SwitchToBlog&RestoreCurrentBlog&GetSiteUrl $wpService,
        private int $blogId
    ) {
        parent::__construct($postObject);
    }

    /**
     * @inheritDoc
     */
    public function getPermalink(): string
    {
        return $this->replaceOriginalSiteUrl(
            $this->addOriginIdentifiersToUrl(
                $this->postObject->getPermalink()
            )
        );
    }

    /**
     * Replace the original site URL with the current site URL.
     *
     * @param string $url The URL to be modified.
     *
     * @return string The modified URL with the original site URL replaced.
     */
    private function replaceOriginalSiteUrl(string $url): string
    {
        return str_replace($this->wpService->getSiteUrl($this->getBlogId()), $this->wpService->getSiteUrl(), $url);
    }

    /**
     * Add the blog ID and post id as query variables to the URL.
     *
     * @param string $url The URL to which the blog ID and post ID will be appended.
     *
     * @return string The URL with the blog ID and post ID query variables appended.
     */
    private function addOriginIdentifiersToUrl(string $url): string
    {
        return $url . (empty(parse_url($url)['query']) ? '?' : '&') . 'blog_id=' . $this->getBlogId() . '&p=' . $this->postObject->getId();
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
    public function getSchemaProperty(string $property): mixed
    {
        return $this->getValueFromOtherBlog(fn() => $this->postObject->getSchemaProperty($property));
    }

    /**
     * @inheritDoc
     */
    public function getSchema(): BaseType
    {
        return $this->getValueFromOtherBlog(fn() => $this->postObject->getSchema());
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
