<?php

namespace Municipio\PostObject;

use AllowDynamicProperties;
use Municipio\PostObject\PostObjectInterface;
use Municipio\PostObject\PostObjectRenderer\PostObjectRendererInterface;

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
    public function __construct(private PostObjectInterface $inner, private object $legacyPost)
    {
        foreach ($this->legacyPost as $key => $value) {
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
    public function getRendered(PostObjectRendererInterface $renderer): string
    {
        return $renderer->render($this);
    }
}
