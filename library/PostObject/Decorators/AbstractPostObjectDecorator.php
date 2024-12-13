<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\PostObjectInterface;

/**
 * Abstract post object decorator.
 */
abstract class AbstractPostObjectDecorator implements PostObjectInterface
{
    protected PostObjectInterface $postObject;

    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return $this->postObject->getId();
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->postObject->getTitle();
    }

    /**
     * @inheritDoc
     */
    public function getPermalink(): string
    {
        return $this->postObject->getPermalink();
    }

    /**
     * @inheritDoc
     */
    public function getCommentCount(): int
    {
        return $this->postObject->getCommentCount();
    }

    /**
     * @inheritDoc
     */
    public function getTermIcons(): array
    {
        return $this->postObject->getTermIcons();
    }
}
