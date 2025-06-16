<?php

namespace Municipio\Integrations\Component;

use ComponentLibrary\Integrations\Image\ImageFocusResolverInterface;
use WpService\Contracts\GetCurrentBlogId;
use WpService\Contracts\SwitchToBlog;

/**
 * Class BlogSwitchedImageFocusResolver
 *
 * This class resolves image focus points for a specific blog, switching to that blog context if necessary.
 */
class BlogSwitchedImageFocusResolver implements ImageFocusResolverInterface
{
  /**
   * Constructor
   */
    public function __construct(private int $blogId, private ImageFocusResolverInterface $innerResolver, private GetCurrentBlogId&SwitchToBlog $wpService)
    {
    }

  /**
   * Get focus point
   *
   * @return array
   */
    public function getFocusPoint(): array
    {
        return $this->maybeRunCallbackInSwitchedMode(fn() => $this->innerResolver->getFocusPoint());
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
