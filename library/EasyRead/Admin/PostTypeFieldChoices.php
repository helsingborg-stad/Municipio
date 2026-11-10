<?php

declare(strict_types=1);

namespace Municipio\EasyRead\Admin;

use WpService\Contracts\AddFilter;
use WpService\Contracts\GetPostTypes;

/** Supplies public post types to the searchable Easy Read settings field. */
final class PostTypeFieldChoices
{
    public function __construct(private AddFilter&GetPostTypes $wpService) {}

    public function addHooks(): void
    {
        $this->wpService->addFilter(
            'acf/load_field/name=easy_reading_posttypes',
            [$this, 'addChoices']
        );
    }

    /**
     * @param array<string, mixed> $field
     * @return array<string, mixed>
     */
    public function addChoices(array $field): array
    {
        $choices = [];

        foreach ($this->wpService->getPostTypes(['public' => true], 'objects') as $postType) {
            if (!is_object($postType) || !is_string($postType->name) || !is_string($postType->label)) {
                continue;
            }

            $choices[$postType->name] = $postType->label;
        }

        natcasesort($choices);
        $field['choices'] = $choices;

        return $field;
    }
}
