<?php

namespace Municipio\Controller\Header;

class ClassesDecorator
{
    public function __construct(private object $data)
    {
    }

    public function decorate(string $setting, string $menu, array $classes): array
    {
        if (!empty($this->data->{$setting}->{$menu})) {
            die;
            return $classes;
        }

        return [];
    }
}
