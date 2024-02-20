<?php

namespace Municipio\Controller\ContentType;

require_once 'Traits/AddSecondaryContentType.php';

/**
 *
 * 'ContentTypeComplexInterface' is designed to be used by classes that
 * manage a collection of simple content types.
 *
 * This is useful for complex classes that are composed of other
 * content types, allowing them to aggregate the functionalities of their components.
 *
 * It's important to note that not all content types require to implement this interface.
 * Simpler content types, such as 'Place', which are not composed of other content types do not need
 * to manage a collection and therefor doesn't need to implement 'ContentTypeComplexInterface'.
 *
 * @package Municipio\Controller\ContentType
 */

interface ContentTypeComplexInterface
{
    /**
     * Adds a ContentTypeComponentInterface instance to the collection.
     *
     * @param ContentTypeComponentInterface $contentType The ContentTypeComponentInterface instance to add.
     * @return void
     */
    // public function addSecondaryContentType(ContentTypeComponentInterface $contentType): void;
}
