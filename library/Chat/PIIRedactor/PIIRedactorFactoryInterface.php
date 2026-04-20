<?php

namespace Municipio\Chat\PIIRedactor;

interface PIIRedactorFactoryInterface
{
    /**
     * Create a PIIRedactor instance
     */
    public function create(): PIIRedactorInterface;
}
