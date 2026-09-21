<?php

declare(strict_types=1);

namespace Modularity\Module\Menu\Decorator;

use Modularity\Module\Menu\Decorator\Listing;

class DataDecorator implements DataDecoratorInterface
{
    private DataDecoratorInterface $dataDecoratorInstance;

    public function __construct(
        private array $fields,
    ) {
        $this->dataDecoratorInstance = $this->getDecoratorInstance();
    }

    private function getDecoratorInstance(): DataDecoratorInterface
    {
        $displayAs = $this->fields['displayAs'] ?? 'listing';

        switch ($displayAs) {
            case 'listing':
                return new Listing();
            default:
                return new Listing();
        }
    }

    public function decorate(array $data): array
    {
        return $this->dataDecoratorInstance->decorate($data);
    }
}
