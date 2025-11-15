<?php

namespace Municipio\PostsList\ViewCallableProviders;

interface ViewCallableProviderInterface
{
    /**
     * Get the callable for the view utility
     *
     * @return callable
     */
    public function getCallable(): callable;
}
