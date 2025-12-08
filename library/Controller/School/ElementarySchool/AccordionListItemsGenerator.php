<?php

namespace Municipio\Controller\School\ElementarySchool;

use Municipio\Controller\School\ViewDataGeneratorInterface;
use Municipio\Schema\ElementarySchool;
use Municipio\Schema\TextObject;
use WpService\Contracts\Wpautop;

class AccordionListItemsGenerator implements ViewDataGeneratorInterface
{
    public function __construct(private ElementarySchool $elementarySchool, private Wpautop $wpService)
    {
    }

    public function generate(): mixed
    {
        return $this->getAccordionListItems($this->elementarySchool->getProperty('description'));
    }

    private function getAccordionListItems(array|string|TextObject|null $description): ?array
    {
        if (!is_array($description) || count($description) <= 1) {
            return null;
        }

        $items = array_map(function ($item) {
            $formattedText = !empty($item->getProperty('text'))
                ? $this->wpService->wpautop($item->getProperty('text'))
                : null;

            return is_a($item, TextObject::class)
                ? ['heading' => $item->getProperty('headline'), 'content' => $formattedText]
                : null;
        }, array_slice($description, 1)); // Skip the first item as it's used for preamble

        return array_filter($items) ?: null;
    }
}
