<?php

namespace Municipio\Controller\Archive;

/**
 * Builder for async attributes/config for archive posts list
 */
class AsyncConfigBuilder
{
    private array $attributes = [];

    public function setQueryVarsPrefix(string $prefix): self
    {
        $this->attributes['queryVarsPrefix'] = $prefix;
        return $this;
    }

    public function setId($id): self
    {
        $this->attributes['id'] = $id;
        return $this;
    }

    public function setPostType(string $postType): self
    {
        $this->attributes['postType'] = $postType;
        return $this;
    }

    public function setDateSource(string $dateSource): self
    {
        $this->attributes['dateSource'] = $dateSource;
        return $this;
    }

    public function setDateFormat(string $dateFormat): self
    {
        $this->attributes['dateFormat'] = $dateFormat;
        return $this;
    }

    public function setNumberOfColumns(int $numberOfColumns): self
    {
        $this->attributes['numberOfColumns'] = $numberOfColumns;
        return $this;
    }

    public function setPostsPerPage(int $postsPerPage): self
    {
        $this->attributes['postsPerPage'] = $postsPerPage;
        return $this;
    }

    public function setPaginationEnabled(bool $paginationEnabled): self
    {
        $this->attributes['paginationEnabled'] = $paginationEnabled;
        return $this;
    }

    public function build(): array
    {
        return $this->attributes;
    }
}
