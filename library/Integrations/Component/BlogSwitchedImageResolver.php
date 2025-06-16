<?php

namespace Municipio\Integrations\Component;

use ComponentLibrary\Integrations\Image\ImageResolverInterface;
use WpService\Contracts\GetCurrentBlogId;
use WpService\Contracts\SwitchToBlog;

class BlogSwitchedImageResolver implements ImageResolverInterface
{
    public function __construct(
        private int $blogId,
        private ImageResolverInterface $innerResolver,
        private GetCurrentBlogId&SwitchToBlog $wpService
    ) {
    }

    /**
     * Get image url
     *
     * @param int $id
     * @param array $size
     * @return string|null
     */
    public function getImageUrl(int $id, array $size): ?string
    {
        return $this->maybeRunCallbackInSwitchedMode(fn() => $this->innerResolver->getImageUrl($id, $size));
    }

    /**
     * Get image alt
     *
     * @param int $id
     * @return string|null
     */
    public function getImageAltText(int $id): ?string
    {
        return $this->maybeRunCallbackInSwitchedMode(fn() => $this->innerResolver->getImageAltText($id));
    }

    /**
     * Executes a callback, switching to the target blog if necessary.
     *
     * @param callable $callback
     * @return mixed
     */
    private function maybeRunCallbackInSwitchedMode(callable $callback)
    {
        $originalBlogId = $this->wpService->getCurrentBlogId();

        if ($originalBlogId === $this->blogId) {
            return $callback();
        }

        $this->wpService->switchToBlog($this->blogId);

        try {
            return $callback();
        } finally {
            $this->wpService->switchToBlog($originalBlogId);
        }
    }
}
