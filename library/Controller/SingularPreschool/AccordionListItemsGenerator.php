<?php

namespace Municipio\Controller\SingularPreschool;

use Municipio\Schema\Preschool;
use Municipio\Schema\TextObject;
use WpService\Contracts\Wpautop;

class AccordionListItemsGenerator
{
    public function __construct(private Preschool $preschool, private Wpautop $wpService)
    {
    }

    public function generate(): mixed
    {
        return $this->getAccordionListItems($this->preschool->getProperty('description'));
    }

    private function getAccordionListItems(array|string|TextObject|null $description): ?array
    {
        if (!is_array($description) || count($description) <= 1) {
            return null;
        }

        $description = array_filter($description, fn ($item) => is_a($item, TextObject::class));
        $description = array_filter($description, fn ($item) => $item->getProperty('name') !== 'visit_us');
        $description = array_map(function ($item) {
            $formattedText = !empty($item->getProperty('text'))
                ? $this->wpService->wpautop($item->getProperty('text'))
                : null;

            return is_a($item, TextObject::class)
                ? ['heading' => $item->getProperty('headline'), 'content' => $formattedText]
                : null;
        }, array_slice($description, 1)); // Skip the first item as it's used for preamble

        return array_filter($description) ?: null;
    }
}
