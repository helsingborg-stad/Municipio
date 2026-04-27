<?php

namespace Municipio\Chat\PIIRedactor;

class PIIRedactorFactory implements PIIRedactorFactoryInterface
{
    public function create(): PIIRedactorInterface
    {
        return new NullPIIRedactor();
    }
}
