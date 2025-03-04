<?php

namespace Municipio\ExternalContent\PropertyPathFilter\FilterDefinition;

use Municipio\ExternalContent\PropertyPathFilter\FilterDefinition\Contracts\FilterDefinition as FilterDefinitionInterface;
use Municipio\ExternalContent\PropertyPathFilter\FilterDefinition\Contracts\RuleSet;

/**
 * Class FilterDefinition
 *
 * Basic implementation of the FilterInterface.
 */
class FilterDefinition implements FilterDefinitionInterface
{
    /**
     * Filter constructor.
     *
     * @param RuleSet[] $ruleSets
     */
    public function __construct(private array $ruleSets)
    {
    }

    /**
     * @inheritDoc
     */
    public function getRuleSets(): array
    {
        return $this->ruleSets;
    }
}
