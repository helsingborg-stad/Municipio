<?php

namespace Municipio\PostObject\Decorators;

use AllowDynamicProperties;
use Municipio\PostObject\PostObjectInterface;

/**
 * Backwards compatible PostObject.
 *
 * This class is used to make sure that the PostObjectInterface is backwards compatible with the old PostObject class.
 */
#[AllowDynamicProperties]
class BackwardsCompatiblePostObject implements PostObjectInterface
{
    /**
     * Constructor.
     */
    public function __construct(private PostObjectInterface $inner, object $legacyPost)
    {
        foreach ($legacyPost as $key => $value) {
            if (!isset($this->{$key})) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return $this->inner->getId();
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->inner->getTitle();
    }

    /**
     * @inheritDoc
     */
    public function getPermalink(): string
    {
        return $this->inner->getPermalink();
    }

    /**
     * @inheritDoc
     */
    public function getCommentCount(): int
    {
        return $this->inner->getCommentCount();
    }
}
