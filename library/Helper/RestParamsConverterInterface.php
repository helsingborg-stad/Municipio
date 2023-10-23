<?php

namespace Municipio\Helper;

interface RestParamsConverterInterface
{
    public static function convertToRestParamsString(array $queryVars): string;
}
