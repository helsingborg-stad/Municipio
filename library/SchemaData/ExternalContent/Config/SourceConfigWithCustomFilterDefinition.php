<?php

namespace Municipio\SchemaData\ExternalContent\Config;

use Municipio\SchemaData\ExternalContent\Filter\FilterDefinition\Contracts\FilterDefinition;

/**
 * Class SourceConfigWithCustomFilterDefinition
 */
class SourceConfigWithCustomFilterDefinition implements SourceConfigInterface
{
    /**
     * SourceConfigWithCustomFilterDefinition constructor.
     *
     * @param FilterDefinition $filterDefinition
     * @param SourceConfigInterface $inner
     */
    public function __construct(private FilterDefinition $filterDefinition, private SourceConfigInterface $inner)
    {
    }

    /**
     * Get the supplied filter definition
     */
    public function getFilterDefinition(): FilterDefinition
    {
        return $this->filterDefinition;
    }

    /**
     * @inheritDoc
     */
    public function getPostType(): string
    {
        return $this->inner->getPostType();
    }

    /**
     * @inheritDoc
     */
    public function getSchemaType(): string
    {
        return $this->inner->getSchemaType();
    }

    /**
     * @inheritDoc
     */
    public function getAutomaticImportSchedule(): string
    {
        return $this->inner->getAutomaticImportSchedule();
    }

    /**
     * @inheritDoc
     */
    public function getSourceType(): string
    {
        return $this->inner->getSourceType();
    }

    /**
     * @inheritDoc
     */
    public function getSourceJsonFilePath(): string
    {
        return $this->inner->getSourceJsonFilePath();
    }

    /**
     * @inheritDoc
     */
    public function getSourceTypesenseApiKey(): string
    {
        return $this->inner->getSourceTypesenseApiKey();
    }

    /**
     * @inheritDoc
     */
    public function getSourceTypesenseProtocol(): string
    {
        return $this->inner->getSourceTypesenseProtocol();
    }

    /**
     * @inheritDoc
     */
    public function getSourceTypesenseHost(): string
    {
        return $this->inner->getSourceTypesenseHost();
    }

    /**
     * @inheritDoc
     */
    public function getSourceTypesensePort(): string
    {
        return $this->inner->getSourceTypesensePort();
    }

    /**
     * @inheritDoc
     */
    public function getSourceTypesenseCollection(): string
    {
        return $this->inner->getSourceTypesenseCollection();
    }

    /**
     * @inheritDoc
     */
    public function getId(): string
    {
        return $this->inner->getId();
    }
}
