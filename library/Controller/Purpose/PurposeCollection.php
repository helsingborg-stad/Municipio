<?php

namespace Municipio\Controller\Purpose;

/**
 * The 'PurposeCollection' Interface
 *
 * This interface is designed to be used by classes that
 * manage a collection of 'PurposeComponent' instances (ie simple purposes).
 *
 * This is particularly useful for complex purpose classes that are composed of other
 * purposes, allowing them to aggregate the functionalities of their components.
 *
 * It's important to note that not all purposes require to implement this interface.
 * Simpler purposes such as 'Place' which are not composed of other purposes do not need
 * to manage a collection and therefor doesn't need to implement 'PurposeCollection'.
 *
 * @package Municipio\Controller\Purpose
 */

interface PurposeCollection
{
    /**
     * Adds a PurposeComponent instance to the collection.
     *
     * @param PurposeComponent $purpose The PurposeComponent instance to add.
     * @return void
     */
    public function addPurpose(PurposeComponent $purpose): void;
}
