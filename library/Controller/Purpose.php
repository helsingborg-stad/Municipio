<?php

namespace Municipio\Controller;

interface Purpose
{
    /**
     * Takes the structured data array, adds a new key/value pair to it, and returns the new array
     *
     * @param structuredData The structured data that's already been set.
     * @param postType The post type of the post you're adding structured data to.
     * @param postId The ID of the post you're adding structured data to.
     *
     * @return An array with the structured data for the project post type.
     */
    public function setStructuredData(array $structuredData = [], string $postType = null, int $postId = null): array;

    /**
     * Return the last part of the class name, in lowercase..
     *
     * @return string The name of the class without the namespace and without the prefix "Archive" or "Singular".
     */
    public static function getKey(): string;

    /**
     * Return the the parent class name, in lowercase.
     *
     * @return string The name of the parent class without the namespace
     */
    public static function getType(): string;
}
