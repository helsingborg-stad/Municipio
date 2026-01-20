<?php

declare(strict_types=1);

namespace Municipio\PostsList\ViewCallableProviders;

use Municipio\PostObject\PostObjectInterface;
use WpService\Contracts\WpTrimWords;

/*
 * View utility to get excerpt with word count trimming
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
        return fn(PostObjectInterface $post, int $nbrOfWords = 55): string => $this->wpService->wpTrimWords(
            $post->getExcerpt(),
            $nbrOfWords,
        );
    }
}
