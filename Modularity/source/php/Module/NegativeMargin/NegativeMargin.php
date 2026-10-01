<?php

declare(strict_types=1);

namespace Modularity\Module\NegativeMargin;

class NegativeMargin extends \Modularity\Module
{
    public $slug = 'negative-margin';
    public $supports = [];

    public function init()
    {
        $this->nameSingular = __('Negative margin', 'municipio');
        $this->namePlural = __('Negative margins', 'municipio');
        $this->description = __('Pulls the following content upward to create an overlap.', 'municipio');
    }

    public function data(): array
    {
        $fields = $this->getFields();

        return [
            'amount' => $this->normalizeAmount($fields['negative_margin_amount'] ?? 4),
        ];
    }

    /**
     * @return array<int, string>
     */
    public function wrapperClasses(): array
    {
        $amount = $this->normalizeAmount($this->data['amount'] ?? 4);

        return ['modularity-negative-margin--' . $amount];
    }

    private function normalizeAmount(mixed $amount): int
    {
        return max(0, min(24, (int) $amount));
    }

    public function template(): string
    {
        return 'negative-margin.blade.php';
    }
}
