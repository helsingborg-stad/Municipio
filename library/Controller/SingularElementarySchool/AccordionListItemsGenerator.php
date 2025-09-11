<?php

namespace Municipio\Controller\SingularElementarySchool;

use Municipio\Schema\ElementarySchool;
use Municipio\Schema\TextObject;

class AccordionListItemsGenerator
{
    public function __construct(private ElementarySchool $elementarySchool)
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
            return is_a($item, TextObject::class)
                ? ['heading' => $item->getProperty('headline'), 'content' => $item->getProperty('text')]
                : null;
        }, array_slice($description, 1)); // Skip the first item as it's used for preamble

        return array_filter($items) ?: null;
    }
}
