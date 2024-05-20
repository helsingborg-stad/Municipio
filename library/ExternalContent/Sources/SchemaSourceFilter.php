<?php

namespace Municipio\ExternalContent\Sources;

interface ISchemaSourceFilter
{
    public function getFilter(): array;
};
