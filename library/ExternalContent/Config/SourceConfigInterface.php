<?php

namespace Municipio\ExternalContent\Config;

interface SourceConfigInterface
{
    /**
     * Get the post type to import to
     *
     * @return string
     */
    public function getPostType(): string;

    /**
     * Get the cron schedule for automatic import
     *
     * @return string
     */
    public function getAutomaticImportSchedule(): string;

    /**
     * Get the taxonomies to import to
     *
     * @return SourceTaxonomyConfigInteface[]
     */
    public function getTaxonomies(): array;

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
}
