<?php

namespace Municipio\Styleguide\Customize\TokenData\Decorators;

interface DecoratorInterface
{
    public function decorate(array $tokenData): array;
}
