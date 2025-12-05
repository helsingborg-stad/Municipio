<?php

namespace Municipio\PostsList\ViewCallableProviders;

use Municipio\PostObject\PostObjectInterface;
use WpService\Contracts\WpTrimWords;

/*
 * View utility to get excerpt without links
 */
class GetExcerpt implements ViewCallableProviderInterface
{
    public function __construct(
        private WpTrimWords $wpService,
    ) {}

    /**
     * Get the callable for the view utility
     *
     * @return callable
     */
    public function getCallable(): callable
    {
        return function (PostObjectInterface $post, int $nbrOfWords = 20): string {
            return $this->wpService->wpTrimWords($post->getExcerpt(), $nbrOfWords);
        };
    }
}
