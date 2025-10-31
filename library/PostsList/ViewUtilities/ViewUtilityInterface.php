<?php

namespace Municipio\PostsList\ViewUtilities;

interface ViewUtilityInterface
{
    /**
     * Get the callable for the view utility
     *
     * @return callable
     */
    public function getCallable(): callable;
}
