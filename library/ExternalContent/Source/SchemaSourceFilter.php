<?php

namespace Municipio\ExternalContent\Source;

interface SchemaSourceFilter
{
    public function getFilter(): array;
};
