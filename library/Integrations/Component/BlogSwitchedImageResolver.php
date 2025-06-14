<?php

namespace Municipio\Integrations\Component;

use ComponentLibrary\Integrations\Image\ImageResolverInterface;

class BlogSwitchedImageResolver implements ImageResolverInterface
{
    public function __construct(private int $blogId, private ImageResolverInterface $innerResolver)
    {
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
        // Switch to the blog context
        switch_to_blog($this->blogId);

        // Get the image URL using the inner resolver
        $imageUrl = $this->innerResolver->getImageUrl($id, $size);

        // Restore the original blog context
        restore_current_blog();

        return $imageUrl;
    }

  /**
   * Get image alt
   *
   * @param int $id
   * @return string|null
   */
    public function getImageAltText(int $id): ?string
    {
        switch_to_blog($this->blogId);
        $result = $this->innerResolver->getImageAltText($id);
        restore_current_blog();
        return $result;
    }
}
