<?php

namespace Municipio\PostsList\QueryVars;

interface QueryVarsInterface
{
    public function getPrefix(): string;
    public function getPaginationParameterName(): string;
    public function getDateFromParameterName(): string;
    public function getDateToParameterName(): string;
    public function getSearchParameterName(): string;
}
