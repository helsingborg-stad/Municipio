<?php

namespace Municipio\Controller\Header;

class Flexible implements HeaderInterface
{
    public function __construct(private object $customizer)
    {
    }

    public function getHeaderData(): array
    {
        $headerData                   = [];
        $headerData['mainUpperItems'] = $this->getMainUpperItems();
        $headerData['mainLowerItems'] = $this->getMainLowerItems();
        $headerData['logotypeItems']  = $this->getLogotypeItems();

        return $headerData;
    }

    private function getLogotypeItems(): array
    {
        return isset($this->customizer->headerSortableSectionLogotype) ? $this->customizer->headerSortableSectionLogotype : [];
    }

    private function getMainLowerItems(): array
    {
        return isset($this->customizer->headerSortableSectionMainLower) ? $this->customizer->headerSortableSectionMainLower : [];
    }

    private function getMainUpperItems(): array
    {
        return isset($this->customizer->headerSortableSectionMainUpper) ? $this->customizer->headerSortableSectionMainUpper : [];
    }
}
