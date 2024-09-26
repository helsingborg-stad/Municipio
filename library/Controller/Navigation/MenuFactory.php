<?php

namespace Municipio\Controller\Navigation;

use Municipio\Controller\Navigation\MenuConfigInterface;
use Municipio\Controller\Navigation\Decorators\MenuItemsDecoratorInterface;
use Municipio\Controller\Navigation\Menu;
use Municipio\Controller\Navigation\Menu2;
use Municipio\Controller\Navigation\Decorators\RemoveTopLevelDecorator;
use Municipio\Controller\Navigation\Decorators\RemoveSubLevelDecorator;
use Municipio\Controller\Navigation\Decorators\StructureMenuItemsDecorator;
use Municipio\Controller\Navigation\Decorators\Default\ComplementDefaultDecorator;
use Municipio\Controller\Navigation\Decorators\PageTreeFallback\ComplementPageTreeDecorator;

class MenuFactory {
    private array $decorators;

    public function __construct(private MenuConfigInterface $menuConfig, private ComplementDefaultDecorator $complementDefaultDecoratorInstance, private ComplementPageTreeDecorator $complementPageTreeDecoratorInstance)
    {
        $this->decorators = [
            new RemoveTopLevelDecorator(),
            new RemoveSubLevelDecorator()
        ];
    }

    public function createMenu(): Menu2 {
        
        return new Menu2(
            $this->menuConfig,
            new StructureMenuItemsDecorator(),
            $this->complementDefaultDecoratorInstance,
            $this->complementPageTreeDecoratorInstance,
            $this->decorators
        );
    }

    public function getDecorators(): array {
        return $this->decorators;
    }

    public function setDecorators(array $decorators): void
    {
        $this->decorators = $decorators;
    }
}