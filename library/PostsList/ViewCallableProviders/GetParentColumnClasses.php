<?php

namespace Municipio\PostsList\ViewCallableProviders;

/*
 * View utility to get parent column classes
 */
class GetParentColumnClasses implements ViewCallableProviderInterface
{
    private array $columnClasses = [
        'o-layout-grid',
        'o-layout-grid--cols-12',
        'o-layout-grid--gap-6',
    ];

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Get the callable for the view utility
     *
     * @return callable
     */
    public function getCallable(): callable
    {
        return fn() => $this->columnClasses;
    }
}
