<?php

declare(strict_types=1);

namespace Modularity\Module\Menu\Decorator;

use Modularity\Module\Menu\Decorator\Listing;
use WpService\Implementations\NativeWpService;

class DataDecorator implements DataDecoratorInterface
{
    private DataDecoratorInterface $dataDecoratorInstance;

    public function __construct(
        private array $fields,
        private NativeWpService|null $wpService
    ) {
        $this->dataDecoratorInstance = $this->getDecoratorInstance();
    }

    private function getDecoratorInstance(): DataDecoratorInterface
    {
        $displayAs = $this->fields['displayAs'] ?? 'listing';

        switch ($displayAs) {
            case 'listing':
            default:
                return new Listing($this->fields, $this->wpService);
        }
    }

    public function decorate(array $data): array
    {
        return $this->dataDecoratorInstance->decorate($data);
    }
}
