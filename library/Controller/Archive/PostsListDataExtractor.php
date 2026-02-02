<?php

namespace Municipio\Controller\Archive;

/**
 * Extracts async configuration data from posts list data.
 *
 * Follows Single Responsibility Principle - only responsible for extracting posts list data.
 */
class PostsListDataExtractor implements AsyncConfigExtractorInterface
{
    private array $postsListData;

    public function __construct(array $postsListData)
    {
        $this->postsListData = $postsListData;
    }

    /**
     * {@inheritDoc}
     */
    public function extract(): array
    {
        return [
            'id' => $this->postsListData['id'] ?? null,
            'asyncId' => $this->postsListData['id'] ?? null,
        ];
    }
}
