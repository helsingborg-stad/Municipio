<?php

namespace Municipio\Controller\School\ElementarySchool;

use Municipio\Controller\School\ViewDataGeneratorInterface;
use Municipio\Schema\ElementarySchool;
use Municipio\Schema\TextObject;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\Wpautop;

class AccordionListItemsGenerator implements ViewDataGeneratorInterface
{
    public function __construct(
        private ElementarySchool $elementarySchool,
        private Wpautop&ApplyFilters $wpService,
    ) {}

    public function generate(): mixed
    {
        return $this->getAccordionListItems($this->elementarySchool->getProperty('description'));
    }

    private function getAccordionListItems(array|string|TextObject|null $description): ?array
    {
        if (!is_array($description) || count($description) <= 1) {
            return null;
        }

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
