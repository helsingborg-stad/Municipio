<?php

namespace Municipio\ExternalContent\Sources;

interface ISourceFilter
{
    public function getFilter(): array;
};
