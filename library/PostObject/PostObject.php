<?php

namespace Municipio\PostObject;

use Municipio\PostObject\PostObjectInterface;

/**
 * PostObject
 */
class PostObject implements PostObjectInterface
{
    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return 0;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getPermalink(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getCommentCount(): int
    {
        return 0;
    }

    /**
     * @inheritDoc
     */
    public function getTermIcons(): array
    {
        return [];
    }
}
