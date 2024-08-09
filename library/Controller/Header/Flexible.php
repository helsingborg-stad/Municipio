<?php

namespace Municipio\Controller\Header;

use Municipio\Controller\Header\MenuOrderTransformer;

class Flexible implements HeaderInterface
{
    private bool $isResponsive;
    private MenuOrderTransformer $menuOrderTransformerInstance;

    public function __construct(private object $customizer)
    {
        $this->isResponsive                 = !empty($this->customizer->headerEnableResponsiveOrder);
        $this->menuOrderTransformerInstance = new MenuOrderTransformer('@md');
    }

    public function getHeaderData(): array
    {
        return [
            'mainUpperItems' => $this->getItems('MainUpper'),
            'mainLowerItems' => $this->getItems('MainLower'),
            'logotypeItems'  => $this->getItems('Logotype'),
        ];
    }

    private function getItems(string $section): array
    {
        $desktopOrderedItems = $this->customizer->{'headerSortableSection' . $section} ?? [];
        $mobileOrderedItems  = ($this->isResponsive && isset($this->customizer->{'headerSortableSection' . $section . 'Responsive'}))
            ? $this->customizer->{'headerSortableSection' . $section . 'Responsive'}
            : [];

        return $this->menuOrderTransformerInstance->transform($desktopOrderedItems, $mobileOrderedItems);
    }
}
