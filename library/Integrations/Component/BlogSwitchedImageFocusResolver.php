<?php

namespace Municipio\Integrations\Component;

use ComponentLibrary\Integrations\Image\ImageFocusResolverInterface;

class BlogSwitchedImageFocusResolver implements ImageFocusResolverInterface
{
  /**
   * Constructor
   */
    public function __construct(private int $blogId, private ImageFocusResolverInterface $innerResolver)
    {
    }

  /**
   * Get focus point
   *
   * @return array
   */
    public function getFocusPoint(): array
    {
        // Switch to the blog context
        switch_to_blog($this->blogId);

        // Get the focus point using the inner resolver
        $focusPoint = $this->innerResolver->getFocusPoint();

        // Restore the original blog context
        restore_current_blog();

        return $focusPoint;
    }
}
