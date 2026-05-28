<?php

namespace Municipio\Chat\PIIRedactor;

use Municipio\Chat\Config\ChatConfigInterface;

interface PIIRedactorFactoryInterface
{
    /**
     * Create a PIIRedactor instance
     */
    public function create(ChatConfigInterface $config): PIIRedactorInterface;
}
