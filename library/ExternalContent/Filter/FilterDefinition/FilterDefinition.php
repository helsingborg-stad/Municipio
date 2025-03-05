<?php

namespace Municipio\ExternalContent\Filter\FilterDefinition;

use Municipio\ExternalContent\Filter\FilterDefinition\Contracts\FilterDefinition as FilterDefinitionInterface;
use Municipio\ExternalContent\Filter\FilterDefinition\Contracts\RuleSet;

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
