<?php

namespace Municipio\Controller\Navigation;

use Municipio\Controller\Navigation\ComplementDecoratorFactoryInterface;
use Municipio\Controller\Navigation\Decorators\MenuItemsDecoratorInterface;
use Municipio\Controller\Navigation\Decorators\Default\ComplementDefaultDecorator;
use Municipio\Controller\Navigation\Decorators\Default\TransformMenuItemDecorator;
use Municipio\Controller\Navigation\Helper\GetAncestorIds;
use Municipio\Controller\Navigation\Decorators\Default\AppendAcfFieldValuesDecorator;
use Municipio\Controller\Navigation\Decorators\Default\AppendIsAncestorDecorator;
use Municipio\Controller\Navigation\Decorators\Default\ApplyMenuItemFilterDecorator;

class ComplementDefaultDecoratorFactory implements ComplementDecoratorFactoryInterface
{
    private array $decorators;

    public function __construct()
    {
        $this->decorators = [
            new AppendAcfFieldValuesDecorator(),
            new AppendIsAncestorDecorator(),
            new ApplyMenuItemFilterDecorator()
        ];
    }

    public function createComplementDecorator(): MenuItemsDecoratorInterface
    {
        return new ComplementDefaultDecorator(
            new TransformMenuItemDecorator(),
            new GetAncestorIds(),
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