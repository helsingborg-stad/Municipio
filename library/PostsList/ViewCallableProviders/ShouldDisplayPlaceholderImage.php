<?php

declare(strict_types=1);

namespace Municipio\PostsList\ViewCallableProviders;

use Municipio\PostObject\PostObjectInterface;

/*
 * View utility to determine if placeholder image should be displayed
 */
class ShouldDisplayPlaceholderImage implements ViewCallableProviderInterface
{
    /** @var PostObjectInterface[] */
    private array $posts;

    public function __construct(
        PostObjectInterface ...$posts,
    ) {
        $this->posts = $posts;
    }

    /**
     * Get the callable for the view utility
     *
     * @return callable
     */
    public function getCallable(): callable
    {
        return [$this, 'shouldDisplayPlaceholderImage'];
    }

    public function shouldDisplayPlaceholderImage(PostObjectInterface $post): bool
    {
        $anyPostHasImage = false;
        foreach ($this->posts as $p) {
            if ($p->getImage() !== null) {
                $anyPostHasImage = true;
                break;
            }
        }

        if ($post->getImage() === null && $anyPostHasImage) {
            return true;
        }

        return false;
    }
}
