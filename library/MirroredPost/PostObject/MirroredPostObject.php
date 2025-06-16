<?php

namespace Municipio\MirroredPost\PostObject;

use Municipio\Integrations\Component\BlogSwitchedImageFocusResolver;
use Municipio\Integrations\Component\BlogSwitchedImageResolver;
use Municipio\Integrations\Component\ImageFocusResolver;
use Municipio\Integrations\Component\ImageResolver;
use Municipio\MirroredPost\Contracts\BlogIdQueryVar;
use Municipio\PostObject\Decorators\AbstractPostObjectDecorator;
use Municipio\PostObject\Icon\IconInterface;
use Municipio\PostObject\PostObjectInterface;
use Municipio\Schema\BaseType;
use ComponentLibrary\Integrations\Image\{Image, ImageInterface};
use WpService\WpService;

/**
 * Decorator for fetching post data from another blog.
 */
class MirroredPostObject extends AbstractPostObjectDecorator implements PostObjectInterface
{
    /**
     * MirroredPostObject constructor.
     */
    public function __construct(
        PostObjectInterface $postObject,
        private WpService $wpService,
        private int $blogId
    ) {
        parent::__construct($postObject);
    }

    /**
     * @inheritDoc
     */
    public function getPermalink(): string
    {
        $seoRedirectMetaUrl = $this->withSwitchedBlog(fn() => $this->wpService->getPostMeta($this->postObject->getId(), 'redirect', true));

        if (filter_var($seoRedirectMetaUrl, FILTER_VALIDATE_URL)) {
            return $seoRedirectMetaUrl;
        }

        $permalink = $this->postObject->getPermalink();
        $permalink = $this->addOriginIdentifiersToUrl($permalink);
        return $this->replaceOriginalSiteUrl($permalink);
    }

    /**
     * Replaces the original site URL with the current site URL in the given URL.
     *
     * @param string $url The URL to modify.
     * @return string The modified URL with the original site URL replaced.
     */
    private function replaceOriginalSiteUrl(string $url): string
    {
        $originalSiteUrl = $this->wpService->getSiteUrl($this->blogId);
        $currentSiteUrl  = $this->wpService->getSiteUrl();
        return str_replace($originalSiteUrl, $currentSiteUrl, $url);
    }

    /**
     * Adds blog ID and post ID as query arguments to the URL.
     *
     * @param string $url The original URL.
     * @return string The modified URL with additional query arguments.
     */
    private function addOriginIdentifiersToUrl(string $url): string
    {
        return $this->wpService->addQueryArg(
            [
                BlogIdQueryVar::BLOG_ID_QUERY_VAR => $this->blogId,
                'p'                               => $this->postObject->getId(),
            ],
            $url
        );
    }

    /**
     * @inheritDoc
     */
    public function getIcon(): ?IconInterface
    {
        return $this->withSwitchedBlog(fn() => $this->postObject->getIcon());
    }

    /**
     * @inheritDoc
     */
    public function getSchemaProperty(string $property): mixed
    {
        return $this->withSwitchedBlog(fn() => $this->postObject->getSchemaProperty($property));
    }

    /**
     * @inheritDoc
     */
    public function getSchema(): BaseType
    {
        return $this->withSwitchedBlog(fn() => $this->postObject->getSchema());
    }

    /**
     * @inheritDoc
     */
    public function getBlogId(): int
    {
        return $this->blogId;
    }

    /**
     * @inheritDoc
     */
    public function getImage(?int $width = null, ?int $height = null): ?ImageInterface
    {
        $imageId = $this->withSwitchedBlog(fn () => $this->wpService->getPostThumbnailId($this->getId()));

        $width  = $width ?? 1920;
        $height = $height ?? false;

        return $imageId !== false ? Image::factory(
            (int) $imageId,
            [$width, $height],
            new BlogSwitchedImageResolver($this->getBlogId(), new ImageResolver(), $this->wpService),
            new BlogSwitchedImageFocusResolver($this->getBlogId(), new ImageFocusResolver(['id' => $imageId]), $this->wpService)
        ) : null;
    }

    /**
     * Executes a callback with the blog switched, restoring afterwards.
     */
    private function withSwitchedBlog(callable $callback): mixed
    {
        $currentBlog = $this->wpService->getCurrentBlogId();

        if ($currentBlog === $this->blogId) {
            return $callback();
        }

        $this->wpService->switchToBlog($this->blogId);

        try {
            return $callback();
        } finally {
            $this->wpService->switchToBlog($currentBlog);
        }
    }
}
