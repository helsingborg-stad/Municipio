<?php

namespace Modularity\Module\Menu\Decorator;

use Modularity\Module\Menu\Decorator\Listing;

class DataDecorator implements DataDecoratorInterface
{
    private DataDecoratorInterface $dataDecoratorInstance;

    public function __construct(private $fields)
    {
        $this->dataDecoratorInstance = $this->getDecoratorInstance($this->fields);

    }

    private function getDecoratorInstance(): DataDecoratorInterface
    {
        $displayAs = $this->fields['displayAs'] ?? 'listing';

            switch ($displayAs) {
                case 'listing':
                    return new Listing($this->fields);
                    break;
                default:
                    return new Listing($this->fields);
                    break;
            }
    }

    public function decorate(array $data): array
    {
        return $this->dataDecoratorInstance->decorate($data);
    }
}