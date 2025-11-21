<?php

declare(strict_types=1);

namespace Modularity\Module\Menu\Decorator;

interface DataDecoratorInterface
{
    public function decorate(array $data): array;
}
