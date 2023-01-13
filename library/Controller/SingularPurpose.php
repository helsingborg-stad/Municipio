<?php

namespace Municipio\Controller;

interface SingularPurpose
{
    /**
     * The localized label used to describe this class in dropdowns and similar circumstances.
     *
     * @return string The label
     */
    public static function getLabel(): string;
}
