<?php

namespace Municipio\PostsList\ViewCallableProviders;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\AppearanceConfig\DateFormat;

/*
 * View utility to show date badge
 */
class ShowDateBadge implements ViewCallableProviderInterface
{
    /**
     * Constructor
     */
    public function __construct(
        private AppearanceConfigInterface $appearanceConfig,
    ) {}

    /**
     * Get the callable for the view utility
     *
     * @return callable
     */
    public function getCallable(): callable
    {
        return function (): bool {
            return $this->appearanceConfig->getDateFormat() === DateFormat::DATE_BADGE;
        };
    }
}
