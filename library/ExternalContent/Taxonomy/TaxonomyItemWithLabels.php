<?php

namespace Municipio\ExternalContent\Taxonomy;

class TaxonomyItemWithLabels implements TaxonomyItemInterface
{
    public function __construct(
        private string $singleLabel,
        private string $pluralLabel,
        private TaxonomyItemInterface $inner
    ) {
    }
    /**
     * @inheritDoc
     */
    public function getSchemaObjectType(): string
    {
        return $this->inner->getSchemaObjectType();
    }

    /**
     * @inheritDoc
     */
    public function getSchemaObjectProperty(): string
    {
        return $this->inner->getSchemaObjectProperty();
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->inner->getName();
    }

    /**
     * @inheritDoc
     */
    public function getSingleLabel(): string
    {
        return $singleLabel;
    }

    /**
     * @inheritDoc
     */
    public function getPluralLabel(): string
    {
        return $pluralLabel;
    }

    /**
     * @inheritDoc
     */
    public function getTaxonomyArgs(): array
    {
        return $this->inner->getTaxonomyArgs();
    }
}
