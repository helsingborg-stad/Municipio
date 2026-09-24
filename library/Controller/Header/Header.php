<?php

namespace Municipio\Controller\Header;

class Header
{
    private function __construct(string $id)
    {
    }

    public static function create(string $id): Header
    {
        return new Header($id);
    }
}