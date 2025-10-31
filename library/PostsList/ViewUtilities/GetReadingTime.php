<?php

namespace Municipio\PostsList\ViewUtilities;

use Municipio\Helper\ReadingTime;
use Municipio\Helper\Memoize\MemoizedFunction;
use Municipio\PostObject\PostObjectInterface;
use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;

/*
 * View utility to get reading time
 */
class GetReadingTime implements ViewUtilityInterface
{
    private MemoizedFunction $memoizedReadingTime;

    /**
     * Constructor
     *
     * @param AppearanceConfigInterface $appearanceConfig
     */
    public function __construct(private AppearanceConfigInterface $appearanceConfig)
    {
        $this->memoizedReadingTime = new MemoizedFunction(
            fn(PostObjectInterface $post) => ReadingTime::getReadingTimeFromPostObject(postObject: $post, i18n: true)
        );
    }

    /**
     * Get the callable for the view utility
     *
     * @return callable
     */
    public function getCallable(): callable
    {
        return function (PostObjectInterface $post): int|string|null {
            return $this->appearanceConfig->shouldDisplayReadingTime()
                ? ($this->memoizedReadingTime)($post)
                : null;
        };
    }
}
