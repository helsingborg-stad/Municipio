<?php

namespace Municipio\ExternalContent\PropertyPathFilter;

use Municipio\ExternalContent\PropertyPathFilter\Contracts\FilterInterface;
use Municipio\ExternalContent\PropertyPathFilter\Contracts\RuleSetInterface;

/**
 * Class Filter
 *
 * Basic implementation of the FilterInterface.
 */
class Filter implements FilterInterface
{
    /**
     * Filter constructor.
     *
     * @param RuleSetInterface[] $ruleSets
     */
    public function __construct(private array $ruleSets)
    {
        if (empty($ruleSets)) {
            throw new \InvalidArgumentException('No rulesets provided.');
        }
    }

    /**
     * @inheritDoc
     */
    public function getRuleSets(): array
    {
        return $this->ruleSets;
    }
}
