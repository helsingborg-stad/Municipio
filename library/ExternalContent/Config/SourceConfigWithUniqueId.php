<?php

namespace Municipio\ExternalContent\Config;

use Municipio\ExternalContent\Filter\FilterDefinition\Contracts\FilterDefinition;

/**
 * Class SourceConfig
 *
 * @package Municipio\ExternalContent\Config
 */
class SourceConfigWithUniqueId implements SourceConfigInterface
{
    private ?string $uniqueId = null;

    public function __construct(private SourceConfigInterface $inner)
    {
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
    public function getAutomaticImportSchedule(): string
    {
        return $this->inner->getAutomaticImportSchedule();
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
    public function getSourceType(): string
    {
        return $this->inner->getSourceType();
    }

    /**
     * @inheritDoc
     */
    public function getTaxonomies(): array
    {
        return $this->inner->getTaxonomies();
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
    public function getFilterDefinition(): FilterDefinition
    {
        return $this->inner->getFilterDefinition();
    }

    /**
     * @inheritDoc
     */
    public function getId(): string
    {
        if ($this->uniqueId === null) {
            $this->uniqueId = $this->generateUniqueId($this->inner->getId());
        }

        return $this->uniqueId;
    }

    private function generateUniqueId(string $id): string
    {
        static $idRegistry = [];

        $id = md5($id);

        if (in_array($id, $idRegistry)) {
            return $this->generateUniqueId($id);
        }

        $idRegistry[] = $id;

        return $id;
    }
}
