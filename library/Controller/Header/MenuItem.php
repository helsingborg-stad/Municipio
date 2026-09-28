<?php

namespace Municipio\Controller\Header;

class MenuItem
{
    public function __construct(
        private string $id,
        private int $index,
        private array $rawMenuItem
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCssClasses(): array
    {
        
        return [];
    }
}