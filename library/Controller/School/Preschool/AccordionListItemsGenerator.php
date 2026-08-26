<?php

namespace Municipio\Controller\School\Preschool;

use Municipio\Controller\School\ViewDataGeneratorInterface;
use Municipio\Helper\EnsureArrayOf\EnsureArrayOf;
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

    private static function isTextObjectValidForUseAsAccordionItem(TextObject $textObject): bool
    {
        $name = $textObject->getProperty('name');
        $headline = $textObject->getProperty('headline');
        $text = $textObject->getProperty('text');

        return !in_array($name, ['role:preamble', 'role:alert', 'visit_us'], true) && !empty($headline) && !empty($text);
    }

    private function getAccordionListItems(array|string|TextObject|null $description): ?array
    {
        if (!is_array($description) || count($description) <= 1) {
            return null;
        }

        $description = array_filter(EnsureArrayOf::ensureArrayOf($description, TextObject::class), [static::class, 'isTextObjectValidForUseAsAccordionItem']);

        return array_map(function (TextObject $item): array {
            $formattedText = !empty($item->getProperty('text')) ? $this->wpService->wpautop($item->getProperty('text')) : null;
            $formattedText = $this->wpService->applyFilters('Municipio\Filters\More', $this->wpService->wpautop($item->getProperty('text')));

            return [
                'heading' => $item->getProperty('headline'),
                'content' => $formattedText,
            ];
        }, $description) ?: null;
    }
}
