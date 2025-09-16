<?php

namespace Municipio\Controller\SingularPreschool;

use Municipio\Schema\Preschool;
use Municipio\Schema\TextObject;
use WpService\Contracts\Wpautop;

class VisitUsGenerator
{
    public function __construct(private Preschool $preschool, private Wpautop $wpService)
    {
    }

    public function generate(): mixed
    {
        $description = $this->preschool->getProperty('description');

        if (!is_array($description) || empty($description)) {
            return null;
        }

        foreach ($description as $item) {
            if (!is_a($item, TextObject::class) || $item->getProperty('name') === 'visit_us') {
                return [
                    'heading' => $item->getProperty('headline'),
                    'content' => !empty($item->getProperty('text'))
                        ? $this->wpService->wpautop($item->getProperty('text'))
                        : null
                ];
            }
        }

        return null;
    }
}
