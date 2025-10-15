<?php

namespace Modularity\Module\Menu\Decorator;

class Listing implements DataDecoratorInterface
{
    public function __construct(private $fields)
    {}

    public function decorate(array $data): array
    {
        $amountOfItems       = !empty($data['menu']['items']) ? count($data['menu']['items']) : 0;
        $data['columns']     = (int) ($this->fields['mod_menu_columns'] ?? 3);
        $data['fakeItems']   = $data['wrapped'] ? ($data['columns'] - ($amountOfItems % $data['columns'])) % $data['columns'] : 0;

        return $data;
    }
}