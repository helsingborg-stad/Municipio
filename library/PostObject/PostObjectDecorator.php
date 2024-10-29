<?php

namespace Municipio\PostObject;

use AllowDynamicProperties;
use Municipio\PostObject\PostObjectInterface;

/**
 * PostObject decorator.
 */
abstract class PostObjectDecorator implements PostObjectInterface
{
    /**
     * Constructor.
     */
    public function __construct(private PostObjectInterface $postObject)
    {
    }

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
}
