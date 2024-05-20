<?php

namespace Municipio\ExternalContent\Source;

interface ISchemaSourceFilter
{
    public function getFilter(): array;
};
