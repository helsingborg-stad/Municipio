<?php

namespace Municipio\SchemaData\ExternalContent\Config;

use Municipio\SchemaData\ExternalContent\Filter\FilterDefinition\Contracts\FilterDefinition;

interface SourceConfigInterface
{
    /**
     * Get the post type to import to
     *
     * @return string
     */
    public function getPostType(): string;

    /**
     * Get the schema type
     *
     * @return string
     */
    public function getSchemaType(): string;

    /**
     * Get the cron schedule for automatic import
     *
     * @return string
     */
    public function getAutomaticImportSchedule(): string;

    /**
     * Get the source type
     *
     * @return string
     */
    public function getSourceType(): string;

    /**
     * Get the source JSON file path
     *
     * @return string
     */
    public function getSourceJsonFilePath(): string;

    /**
     * Get the Typesense API key
     *
     * @return string
     */
    public function getSourceTypesenseApiKey(): string;

    /**
     * Get the Typesense protocol
     *
     * @return string
     */
    public function getSourceTypesenseProtocol(): string;

    /**
     * Get the Typesense host
     *
     * @return string
     */
    public function getSourceTypesenseHost(): string;

    /**
     * Get the Typesense port
     *
     * @return string
     */
    public function getSourceTypesensePort(): string;

    /**
     * Get the Typesense collection
     *
     * @return string
     */
    public function getSourceTypesenseCollection(): string;

    /**
     * Get the source unique ID
     */
    public function getId(): string;

    /**
     * Retrieves the filter definition.
     *
     * @return FilterDefinition The filter definition.
     */
    public function getFilterDefinition(): FilterDefinition;
}
