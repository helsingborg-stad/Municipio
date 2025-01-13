<?php

namespace Municipio\PostObject;

use Municipio\PostObject\Icon\IconInterface;
use Municipio\PostObject\PostObjectInterface;
use Municipio\PostObject\TermIcon\TermIconInterface;

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
    public function getPostType(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getIcon(): ?IconInterface
    {
        return null;
    }
}
