<?php

namespace Municipio\PostsList\QueryVars;

class QueryVars implements QueryVarsInterface
{
    private static array $usedPrefixes = [];

    public function __construct(private string $prefix)
    {
        if (in_array($this->prefix, self::$usedPrefixes)) {
            throw new \Exception('Prefix may not be reused between instances');
        }

        self::$usedPrefixes[] = $this->prefix;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function getPaginationParameterName(): string
    {
        return $this->prefixParameter('page');
    }

    public function getDateFromParameterName(): string
    {
        return $this->prefixParameter('date_from');
    }

    public function getDateToParameterName(): string
    {
        return $this->prefixParameter('date_to');
    }

    public function getSearchParameterName(): string
    {
        return $this->prefixParameter('search');
    }

    private function prefixParameter(string $param): string
    {
        return $this->getPrefix() . $param;
    }
}
