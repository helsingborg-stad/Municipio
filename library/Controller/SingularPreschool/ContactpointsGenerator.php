<?php

namespace Municipio\Controller\SingularPreschool;

use Municipio\Schema\Preschool;

class ContactpointsGenerator implements ViewDataGeneratorInterface
{
    public function __construct(private Preschool $preschool)
    {
    }

    public function generate(): mixed
    {
        $contactPoints = $this->preschool->getProperty('contactPoint');

        if (!is_array($contactPoints) || empty($contactPoints)) {
            return [];
        }

        $contactPoints = array_filter($contactPoints, function ($contactPoint) {
            return
                is_string($contactPoint->getProperty('url')) &&
                !empty($contactPoint->getProperty('url')) &&
                $contactPoint->getProperty('contactType') === 'socialmedia' &&
                !empty($contactPoint->getProperty('name'));
        });

        return [
            'items' => array_map(function ($contactPoint) {
                return [
                    'name' => $contactPoint->getProperty('name'),
                    'url'  => $contactPoint->getProperty('url'),
                    'icon' => $contactPoint->getProperty('name')
                ];
            }, $contactPoints),
        ];
    }
}
