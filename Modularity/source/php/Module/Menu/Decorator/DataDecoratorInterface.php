<?php

namespace Modularity\Module\Menu\Decorator;

interface DataDecoratorInterface
{
    public function decorate(array $data): array;
}