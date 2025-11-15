<?php

namespace Municipio\PostsList\QueryVars;

interface QueryVarsInterface
{
    /**
     * Get prefix
     */
    public function getPrefix(): string;

    /**
     * Get pagination parameter name
     */
    public function getPaginationParameterName(): string;

    /**
     * Get date from parameter name
     */
    public function getDateFromParameterName(): string;

    /**
     * Get date to parameter name
     */
    public function getDateToParameterName(): string;

    /**
     * Get search parameter name
     */
    public function getSearchParameterName(): string;
}
