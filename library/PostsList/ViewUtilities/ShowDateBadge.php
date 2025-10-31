<?php

namespace Municipio\PostsList\ViewUtilities;

use Municipio\Helper\Memoize\MemoizedFunction;
use Municipio\PostObject\PostObjectInterface;

/*
 * View utility to show date badge
 */
class ShowDateBadge implements ViewUtilityInterface
{
    private MemoizedFunction $memoizedShowDateBadge;

    /**
     * Constructor
     *
     * @param PostObjectInterface[] $posts
     */
    public function __construct(private array $posts)
    {
        $this->memoizedShowDateBadge = new MemoizedFunction(
            fn(array $posts) => count(array_filter($posts, fn(PostObjectInterface $post) => \Municipio\Helper\DateFormat::getUnresolvedDateFormat($post) == 'date-badge')) > 0
        );
    }

    /**
     * Get the callable for the view utility
     *
     * @return callable
     */
    public function getCallable(): callable
    {
        return function (): bool {
            return ($this->memoizedShowDateBadge)($this->posts);
        };
    }
}
