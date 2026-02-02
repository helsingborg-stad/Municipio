<?php

namespace Municipio\Controller\Archive;

/**
 * Interface for building async configuration arrays.
 *
 * Follows Dependency Inversion Principle - depend on abstractions, not concretions.
 */
interface AsyncConfigBuilderInterface
{
    /**
     * Set the query variables prefix.
     *
     * @param string $prefix The prefix for query variables
     * @return self
     */
    public function setQueryVarsPrefix(string $prefix): self;

    /**
     * Set the unique identifier.
     *
     * @param string|null $id The unique identifier
     * @return self
     */
    public function setId(?string $id): self;

    /**
     * Set the post type.
     *
     * @param string|null $postType The post type
     * @return self
     */
    public function setPostType(?string $postType): self;

    /**
     * Set the date source field.
     *
     * @param string $dateSource The date source field name
     * @return self
     */
    public function setDateSource(string $dateSource): self;

    /**
     * Set the date format.
     *
     * @param string $dateFormat The date format
     * @return self
     */
    public function setDateFormat(string $dateFormat): self;

    /**
     * Set the number of columns.
     *
     * @param int $numberOfColumns The number of columns
     * @return self
     */
    public function setNumberOfColumns(int $numberOfColumns): self;

    /**
     * Set the number of posts per page.
     *
     * @param int $postsPerPage The number of posts per page
     * @return self
     */
    public function setPostsPerPage(int $postsPerPage): self;

    /**
     * Set whether pagination is enabled.
     *
     * @param bool $enabled Whether pagination is enabled
     * @return self
     */
    public function setPaginationEnabled(bool $enabled): self;

    /**
     * Set the async ID.
     *
     * @param string|null $asyncId The async ID
     * @return self
     */
    public function setAsyncId(?string $asyncId): self;

    /**
     * Set whether this is an async request.
     *
     * @param bool $isAsync Whether this is an async request
     * @return self
     */
    public function setIsAsync(bool $isAsync): self;

    /**
     * Build and return the configuration array.
     *
     * @return array The built configuration
     */
    public function build(): array;

    /**
     * Reset the builder to its initial state.
     *
     * @return self
     */
    public function reset(): self;
}
