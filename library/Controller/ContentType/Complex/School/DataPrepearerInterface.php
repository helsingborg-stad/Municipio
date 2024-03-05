<?php

namespace Municipio\Controller\ContentType\Complex\School;

interface DataPrepearerInterface
{
    public function prepareData(array $data): array;
}
