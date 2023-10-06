<?php

namespace Municipio\Controller\ContentType;

/**
 * 'ContentTypeComponentInterface' defines the standard functionality that
 * every content type, both simple and complex, should implement.
 *
 * It's the core contract that all content types in the project adhere to,
 * ensuring consistency and interoperability among all different types
 * and it is implemented by the 'ContentTypeFactory' class that all content types should inherit.
 */

interface ContentTypeComponentInterface
{
    /**
     * Gets the label for the content type component.
     *
     * @return string The label.
     */
    public function getLabel(): string;

    /**
     * Gets the key for the content type component.
     *
     * @return string The key.
     */
    public function getKey(): string;

    /**
     * Gets the view for the content type component.
     *
     * @return string The view.
     */
    public function getView(): string;
}
