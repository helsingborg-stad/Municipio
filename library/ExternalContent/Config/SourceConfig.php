<?php

namespace Municipio\ExternalContent\Config;

use Municipio\ExternalContent\Filter\FilterDefinition\Contracts\FilterDefinition;

/**
 * Class SourceConfig
 *
 * @package Municipio\ExternalContent\Config
 */
class SourceConfig implements SourceConfigInterface
{
    /**
     * SourceConfig constructor.
     *
     * @param string $postType
     * @param string $automaticImportSchedule
     * @param string $schemaType
     * @param string $sourceType
     * @param string $sourceJsonFilePath
     * @param string $sourceTypesenseApiKey
     * @param string $sourceTypesenseProtocol
     * @param string $sourceTypesenseHost
     * @param string $sourceTypesensePort
     * @param string $sourceTypesenseCollection
     */
    public function __construct(
        private string $postType,
        private string $automaticImportSchedule,
        private string $schemaType,
        private string $sourceType,
        private string $sourceJsonFilePath,
        private string $sourceTypesenseApiKey,
        private string $sourceTypesenseProtocol,
        private string $sourceTypesenseHost,
        private string $sourceTypesensePort,
        private string $sourceTypesenseCollection,
        private FilterDefinition $filterDefinition
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getPostType(): string
    {
        return $this->postType;
    }

    /**
     * @inheritDoc
     */
    public function getAutomaticImportSchedule(): string
    {
        return $this->automaticImportSchedule;
    }

    /**
     * @inheritDoc
     */
    public function getSchemaType(): string
    {
        return $this->schemaType;
    }

    /**
     * @inheritDoc
     */
    public function getSourceType(): string
    {
        return $this->sourceType;
    }

    /**
     * @inheritDoc
     */
    public function getSourceJsonFilePath(): string
    {
        return $this->sourceJsonFilePath;
    }

    /**
     * @inheritDoc
     */
    public function getSourceTypesenseApiKey(): string
    {
        return $this->sourceTypesenseApiKey;
    }

    /**
     * @inheritDoc
     */
    public function getSourceTypesenseProtocol(): string
    {
        return $this->sourceTypesenseProtocol;
    }

    /**
     * @inheritDoc
     */
    public function getSourceTypesenseHost(): string
    {
        return $this->sourceTypesenseHost;
    }

    /**
     * @inheritDoc
     */
    public function getSourceTypesensePort(): string
    {
        return $this->sourceTypesensePort;
    }

    /**
     * @inheritDoc
     */
    public function getSourceTypesenseCollection(): string
    {
        return $this->sourceTypesenseCollection;
    }

    /**
     * @inheritDoc
     */
    public function getId(): string
    {
        return $this->postType;
    }

    /**
     * @inheritDoc
     */
    public function getFilterDefinition(): FilterDefinition
    {
        return $this->filterDefinition;
    }
}
