<?php

namespace Municipio\Controller\Navigation;

use Municipio\Controller\Navigation\ComplementDecoratorFactoryInterface;
use Municipio\Controller\Navigation\Decorators\MenuItemsDecoratorInterface;
use Municipio\Controller\Navigation\Decorators\Default\ComplementDefaultDecorator;
use Municipio\Controller\Navigation\Decorators\Default\TransformMenuItemDecorator;
use Municipio\Controller\Navigation\Helper\GetAncestorIds;

class ComplementDefaultDecoratorFactory implements ComplementDecoratorFactoryInterface
{
    private array $decorators;

    public function __construct(private MenuConfigInterface $menuConfig)
    {
        $this->decorators = [];
    }

    public function createComplementDecorator(): MenuItemsDecoratorInterface
    {
        return new ComplementDefaultDecorator(
            new TransformMenuItemDecorator($this->menuConfig),
            new GetAncestorIds($this->menuConfig),
            $this->decorators
        );
    }

    public function getDecorators(): array
    {
        return $this->decorators;
    }

    public function setDecorators(array $decorators): void
    {
        $this->decorators = $decorators;
    }
}