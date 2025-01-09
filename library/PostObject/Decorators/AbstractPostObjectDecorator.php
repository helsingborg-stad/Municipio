<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\Icon\IconInterface;
use Municipio\PostObject\PostObjectInterface;
use Municipio\PostObject\TermIcon\TermIconInterface;

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

    /**
     * @inheritDoc
     */
    public function getPostType(): string
    {
        return $this->postObject->getPostType();
    }

    /**
     * @inheritDoc
     */
    public function getTermIcon(?string $taxonomy = null): ?TermIconInterface
    {
        return $this->postObject->getTermIcon($taxonomy);
    }

    /**
     * @inheritDoc
     */
    public function getIcon(): ?IconInterface
    {
        return $this->postObject->getIcon();
    }
}
