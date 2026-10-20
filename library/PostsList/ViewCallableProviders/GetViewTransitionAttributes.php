<?php

declare(strict_types=1);

namespace Municipio\PostsList\ViewCallableProviders;

use Municipio\ViewTransition\PostTargets;

/**
 * Provides a post's shared view-transition target attributes to list templates.
 */
class GetViewTransitionAttributes implements ViewCallableProviderInterface
{
    public function getCallable(): callable
    {
        return PostTargets::attributes(...);
    }
}
