<?php

namespace Municipio\Controller\Archive;

/**
 * Builder for async configuration attributes for archive posts list.
 *
 * Follows Single Responsibility Principle - only responsible for building async config.
 * Follows Open/Closed Principle - extensible through adding new methods without modifying existing code.
 */
class AsyncConfigBuilder implements AsyncConfigBuilderInterface
{
    private ?string $queryVarsPrefix = null;
    private ?string $id = null;
    private ?string $postType = null;
    private string $dateSource = 'post_date';
    private string $dateFormat = 'date-time';
    private int $numberOfColumns = 1;
    private int $postsPerPage = 10;
    private bool $paginationEnabled = true;
    private ?string $asyncId = null;
    private bool $isAsync = false;
    private array $sourceAttributes = [];

    /**
     * {@inheritDoc}
     */
    public function setQueryVarsPrefix(string $prefix): self
    {
        $this->queryVarsPrefix = $prefix;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setId(?string $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setPostType(?string $postType): self
    {
        $this->postType = $postType;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setDateSource(string $dateSource): self
    {
        $this->dateSource = $dateSource;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setDateFormat(string $dateFormat): self
    {
        $this->dateFormat = $dateFormat;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setNumberOfColumns(int $numberOfColumns): self
    {
        $this->numberOfColumns = $numberOfColumns;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setPostsPerPage(int $postsPerPage): self
    {
        $this->postsPerPage = $postsPerPage;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setPaginationEnabled(bool $enabled): self
    {
        $this->paginationEnabled = $enabled;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setAsyncId(?string $asyncId): self
    {
        $this->asyncId = $asyncId;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setIsAsync(bool $isAsync): self
    {
        $this->isAsync = $isAsync;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setSourceAttributes(array $attributes): self
    {
        $this->sourceAttributes = $attributes;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function build(): array
    {
        // Build ULTRA-MINIMAL async config with ONLY essential identifiers for URL
        // Returns ONLY: postType, queryVarsPrefix, archivePropsKey
        // Backend will reconstruct EVERYTHING else when async request comes in
        return $this->sourceAttributes; // Contains ONLY the 3 minimal identifiers
    }

    /**
     * {@inheritDoc}
     */
    public function reset(): self
    {
        $this->queryVarsPrefix = null;
        $this->id = null;
        $this->postType = null;
        $this->dateSource = 'post_date';
        $this->dateFormat = 'date-time';
        $this->numberOfColumns = 1;
        $this->postsPerPage = 10;
        $this->paginationEnabled = true;
        $this->asyncId = null;
        $this->isAsync = false;
        $this->sourceAttributes = [];
        return $this;
    }
}
