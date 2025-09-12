<?php

namespace Municipio\Controller\SingularElementarySchool;

use Municipio\Schema\ElementarySchool;

class ActionsGenerator implements ViewDataGeneratorInterface
{
    public function __construct(private ElementarySchool $elementarySchool)
    {
    }

    public function generate(): mixed
    {
        if (!is_array($this->elementarySchool->getProperty('potentialAction'))) {
            return [];
        }

        $actions = array_filter($this->elementarySchool->getProperty('potentialAction'), fn($fn) => is_a($fn, \Municipio\Schema\Action::class));
        return [
            'description' => $actions[0]?->getProperty('description') ?? null,
            'buttonsArgs' => array_map(fn($action, $index) => [
                'text'  => $action->getProperty('title'),
                'href'  => $action->getProperty('url'),
                'color' => $index === 0  ? 'primary' : 'secondary',
            ], $actions, array_keys($actions)),
        ];
    }
}
