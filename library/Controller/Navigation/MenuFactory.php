<?php

namespace Municipio\Controller\Navigation;

use Municipio\Controller\Navigation\MenuConfigInterface;
use Municipio\Controller\Navigation\Decorators\MenuItemsDecoratorInterface;
use Municipio\Controller\Navigation\Menu;
use Municipio\Controller\Navigation\Decorators\RemoveTopLevelDecorator;
use Municipio\Controller\Navigation\Decorators\RemoveSubLevelDecorator;
use Municipio\Controller\Navigation\Decorators\StructureMenuItemsDecorator;

class MenuFactory implements MenuFactoryInterface {
    private array $decorators;

    public function __construct(private MenuConfigInterface $menuConfig)
    {
        $this->decorators = [
            new RemoveTopLevelDecorator($this->menuConfig),
            new RemoveSubLevelDecorator($this->menuConfig)
        ];
    }

    public function createMenu(): Menu {
        
        return new Menu(
            $this->menuConfig,
            new StructureMenuItemsDecorator($this->menuConfig),
        );
    }

    public function getDecorators(): array {
        return $this->decorators;
    }

    public function setDecorators(MenuItemsDecoratorInterface ...$decorators): void
    {
        
    }
}