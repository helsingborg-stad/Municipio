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
class BackwardsCompatiblePostObject extends AbstractPostObjectDecorator implements PostObjectInterface
{
    private const PROPERTY_TO_METHOD_MAP = ['permalink' => 'getPermalink'];

    /**
     * Constructor.
     */
    public function __construct(PostObjectInterface $postObject, private object $legacyPost)
    {
        parent::__construct($postObject);

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
     * @inheritDoc
     */
    public function __get(string $name): mixed
    {
        if (array_key_exists($name, self::PROPERTY_TO_METHOD_MAP)) {
            return $this->{self::PROPERTY_TO_METHOD_MAP[$name]}();
        }

        return $this->postObject->__get($name);
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
}
