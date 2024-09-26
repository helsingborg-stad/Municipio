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
    private GetAncestorIds $getAncestorsInstance;
    private TransformMenuItemDecorator $transformMenuItemDecorator;

    public function __construct()
    {
        [
            $getAncestorsInstance,
            $transformMenuItemDecorator,
            $decorators
        ] = $this->getClassProperties();

        // Assign class properties
        $this->getAncestorsInstance = $getAncestorsInstance;
        $this->transformMenuItemDecorator = $transformMenuItemDecorator;
        $this->decorators = $decorators;
    }

    public static function createComplementDecoratorStatic(): MenuItemsDecoratorInterface
    {
        
        return new ComplementDefaultDecorator(
            ,
            self::getDefaultDecorators()
        );
    }

    public function getClassProperties(): array
    {
        return [
            new GetAncestorIds(),
            new TransformMenuItemDecorator(),
            $this->getDefaultDecorators()
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

    private function getDefaultDecorators(): array
    {
        return [
            new AppendAcfFieldValuesDecorator(),
            new AppendIsAncestorDecorator(),
            new ApplyMenuItemFilterDecorator()
        ];
    }
}