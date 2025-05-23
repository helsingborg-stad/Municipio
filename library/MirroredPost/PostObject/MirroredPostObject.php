<?php

namespace Municipio\MirroredPost\PostObject;

use Municipio\MirroredPost\Contracts\BlogIdQueryVar;
use Municipio\PostObject\Decorators\AbstractPostObjectDecorator;
use Municipio\PostObject\Icon\IconInterface;
use Municipio\PostObject\PostObjectInterface;
use Municipio\Schema\BaseType;
use WpService\Contracts\AddQueryArg;
use WpService\Contracts\GetSiteUrl;
use WpService\Contracts\RestoreCurrentBlog;
use WpService\Contracts\SwitchToBlog;

/**
 * Post object decorator that can fetch post data from another blog.
 * If the post is from another blog, it will switch to that blog to fetch the data.
 */
class MirroredPostObject extends AbstractPostObjectDecorator implements PostObjectInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        PostObjectInterface $postObject,
        private SwitchToBlog&RestoreCurrentBlog&GetSiteUrl&AddQueryArg $wpService,
        private int $blogId
    ) {
        parent::__construct($postObject);
    }

    /**
     * @inheritDoc
     */
    public function getPermalink(): string
    {
        $permalink                = $this->postObject->getPermalink();
        $permalinkWithIdentifiers = $this->addOriginIdentifiersToUrl($permalink);
        return $this->replaceOriginalSiteUrl($permalinkWithIdentifiers);
    }

    /**
     * Replace the site URL of the original blog with the current site URL in the given URL.
     *
     * @param string $url The URL to modify.
     * @return string The URL with the original blog's site URL replaced by the current site URL.
     */
    private function replaceOriginalSiteUrl(string $url): string
    {
        $originalSiteUrl = $this->wpService->getSiteUrl($this->getBlogId());
        $currentSiteUrl  = $this->wpService->getSiteUrl();

        return str_replace($originalSiteUrl, $currentSiteUrl, $url);
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
        $blogId = $this->getBlogId();
        $postId = $this->postObject->getId();

        return $this->wpService->addQueryArg(
            [
                BlogIdQueryVar::BLOG_ID_QUERY_VAR => $blogId,
                'p'                               => $postId,
            ],
            $url
        );
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
