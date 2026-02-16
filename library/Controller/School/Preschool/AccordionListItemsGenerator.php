<?php

namespace Municipio\Controller\School\Preschool;

use Municipio\Controller\School\ViewDataGeneratorInterface;
use Municipio\Schema\Preschool;
use Municipio\Schema\TextObject;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\Wpautop;

class AccordionListItemsGenerator implements ViewDataGeneratorInterface
{
    public function __construct(
        private Preschool $preschool,
        private Wpautop&ApplyFilters $wpService,
    ) {}

    public function generate(): mixed
    {
        return $this->getAccordionListItems($this->preschool->getProperty('description'));
    }

    private function getAccordionListItems(array|string|TextObject|null $description): ?array
    {
        if (!is_array($description) || count($description) <= 1) {
            return null;
        }

        $description = array_filter($description, fn($item) => is_a($item, TextObject::class));
        $description = array_filter($description, fn($item) => $item->getProperty('name') !== 'visit_us');

        $result = [];

        foreach (array_slice($description, 1) as $item) {
            if (!$item instanceof TextObject) {
                continue;
            }

            $formattedText = !empty($item->getProperty('text')) ? $this->wpService->wpautop($item->getProperty('text')) : null;
            $formattedText = $this->wpService->applyFilters('Municipio\Filters\More', $this->wpService->wpautop($item->getProperty('text')));

            $result[] = [
                'heading' => $item->getProperty('headline'),
                'content' => $formattedText,
            ];
        }

        return $result ?: null;
    }
}
