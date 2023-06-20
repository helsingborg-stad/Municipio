<?php

namespace Municipio\Controller\Purpose;

/**
 *
 * 'PurposeCollectionInterface' is designed to be used by classes that
 * manage a collection of simple purposes.
 *
 * This is useful for complex purpose classes that are composed of other
 * purposes, allowing them to aggregate the functionalities of their components.
 *
 * It's important to note that not all purposes require to implement this interface.
 * Simpler purposes, such as 'Place', which are not composed of other purposes do not need
 * to manage a collection and therefor doesn't need to implement 'PurposeCollectionInterface'.
 *
 * @package Municipio\Controller\Purpose
 */

interface PurposeCollectionInterface
{
    /**
     * Adds a PurposeComponentInterface instance to the collection.
     *
     * @param PurposeComponentInterface $purpose The PurposeComponentInterface instance to add.
     * @return void
     */
    public function addSecondaryPurpose(PurposeComponentInterface $purpose): void;
}
