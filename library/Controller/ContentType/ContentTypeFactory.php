<?php

namespace Municipio\Controller\ContentType;

/**
 * The ContentTypeFactory Class
 * ----------------------------
 * This class is a basic implementation of the 'ContentTypeComponentInterface' interface.
 *
 * It provides default methods and properties for a content type and
 * is designed to be a base class for all content type classes in the project.
 *
 * Simple content types:
 * ---------------------
 * A simple content type class that doesn't need to load other types
 * should extend this class to gain basic functionality.
 * An example of a simple content type is the default type 'Place'.
 *
 * Complex content types:
 * ----------------------
 * A complex content type is a class that needs to load other types and
 * it should extend this class but also implement the 'ContentTypeComplexInterface'
 * interface.
 * An example of a complex purpose is the default 'Event'.
 *
 */
class ContentTypeFactory implements ContentTypeComponentInterface
{
    protected string $key;
    protected string $label;

    public function __construct(string $key, string $label)
    {
        $this->key              = $key;
        $this->label            = $label;
    }

    public function getLabel(): string
    {
        return $this->label;
    }
    public function getKey(): string
    {
        return $this->key;
    }
    public function getView(): string
    {
        return "content-type-{$this->getKey()}";
    }
}
