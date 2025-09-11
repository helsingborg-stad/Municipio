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
        echo '<pre>' . print_r($this->elementarySchool, true) . '</pre>';
        die;

        return [];
    }
}
