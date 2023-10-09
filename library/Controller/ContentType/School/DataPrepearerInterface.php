<?php

namespace Municipio\Controller\ContentType\School;

interface DataPrepearerInterface
{
    public function prepareData(array $data): array;
}
