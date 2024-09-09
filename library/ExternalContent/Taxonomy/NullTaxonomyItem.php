<?php

namespace Municipio\ExternalContent\Taxonomy;

class NullTaxonomyItem implements TaxonomyItemInterface
{
    /**
     * Class constructor.
     */
    public function __construct()
    {
    }

    /**
     * @inheritDoc
     */
    public function getSchemaObjectType(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getPostTypes(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getSchemaObjectProperty(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getSingleLabel(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getPluralLabel(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getTaxonomyArgs(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
    }
}
