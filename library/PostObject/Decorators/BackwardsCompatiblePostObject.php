<?php

namespace Municipio\PostObject\Decorators;

use AllowDynamicProperties;
use Municipio\PostObject\Icon\IconInterface;
use Municipio\PostObject\PostObjectInterface;

/**
 * Backwards compatible PostObject.
 *
 * This class is used to make sure that the PostObjectInterface is backwards compatible with the old PostObject class.
 */
#[AllowDynamicProperties]
class BackwardsCompatiblePostObject implements PostObjectInterface
{
    private const PROPERTY_TO_METHOD_MAP = ['permalink' => 'getPermalink'];

    /**
     * Constructor.
     */
    public function __construct(private PostObjectInterface $postObject, private object $legacyPost)
    {
        foreach ($legacyPost as $key => $value) {
            if (array_key_exists($key, self::PROPERTY_TO_METHOD_MAP)) {
                continue;
            }

            if (!isset($this->{$key})) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * Magic getter.
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (array_key_exists($name, self::PROPERTY_TO_METHOD_MAP)) {
            return $this->{self::PROPERTY_TO_METHOD_MAP[$name]}();
        }
    }

    /**
     * Magic setter.
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        if (array_key_exists($name, self::PROPERTY_TO_METHOD_MAP)) {
            return;
        }

        $this->{$name} = $value;
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
    public function getPostType(): string
    {
        return $this->postObject->getPostType();
    }

    /**
     * @inheritDoc
     */
    public function getIcon(): ?IconInterface
    {
        return $this->postObject->getIcon();
    }

    /**
     * @inheritDoc
     */
    public function getBlogId(): int
    {
        return $this->postObject->getBlogId();
    }

    /**
     * @inheritDoc
     */
    public function getPublishedTime(bool $gmt = false): int
    {
        return $this->postObject->getPublishedTime($gmt);
    }

    /**
     * @inheritDoc
     */
    public function getModifiedTime(bool $gmt = false): int
    {
        return $this->postObject->getModifiedTime($gmt);
    }

    /**
     * @inheritDoc
     */
    public function getArchiveDateTimestamp(): ?int
    {
        return $this->postObject->getArchiveDateTimestamp();
    }

    /**
     * @inheritDoc
     */
    public function getArchiveDateFormat(): string
    {
        return $this->postObject->getArchiveDateFormat();
    }
}
