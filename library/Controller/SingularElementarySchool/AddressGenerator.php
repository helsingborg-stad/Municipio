<?php

namespace Municipio\Controller\SingularElementarySchool;

use Municipio\Schema\ElementarySchool;

class AddressGenerator implements ViewDataGeneratorInterface
{
    public function __construct(private ElementarySchool $elementarySchool)
    {
    }

    public function generate(): mixed
    {
        return is_string($this->elementarySchool->getProperty('address'))
            ? $this->elementarySchool->getProperty('address')
            : null;
    }
}
