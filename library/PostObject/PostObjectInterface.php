<?php

namespace Municipio\PostObject;

use Municipio\ImageConvert\Contract\ImageContractInterface;
use Municipio\PostObject\Icon\IconInterface;
use Municipio\PostObject\Image\ImageInterface;
use Municipio\Schema\BaseType;

interface PostObjectInterface
{
    /**
     * Get the post object ID.
     *
     * @return int
     */
    public function getId(): int;

    /**
     * Get the post object title.
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Get the post object excerpt.
     *
     * @return string
     */
    public function getPermalink(): string;

    /**
     * Get the amount of comments on the post.
     *
     * @return int
     */
    public function getCommentCount(): int;

    /**
     * Get the post type.
     */
    public function getPostType(): string;

    /**
     * Get the post object icon.
     *
     * @return IconInterface|null The post object icon or null if none is found.
     */
    public function getIcon(): ?IconInterface;

    /**
     * Get the post object blog id.
     * Returns the blog id of the post objects origin.
     *
     * @return int
     */
    public function getBlogId(): int;

    /**
     * Get the post object publish timestamp.
     *
     * @param bool $gmt Whether to return the GMT time.
     *
     * @return int
     */
    public function getPublishedTime(bool $gmt = false): int;

    /**
     * Get the post object modified timestamp.
     *
     * @param bool $gmt Whether to return the GMT time.
     *
     * @return int
     */
    public function getModifiedTime(bool $gmt = false): int;

    /**
     * Get the post object date timestamp.
     *
     * @return int
     */
    public function getArchiveDateTimestamp(): ?int;

    /**
     * Get the post object date format.
     *
     * @return string
     */
    public function getArchiveDateFormat(): string;

    /**
     * Get schema property.
     *
     * @param string $property The schema property to get.
     * @return mixed The value of the schema property. Will return null if the property does not exist.
     */
    public function getSchemaProperty(string $property): mixed;

    /**
     * Get the schema object.
     *
     * @return BaseType The schema object.
     */
    public function getSchema(): BaseType;

    /**
     * Get the post object terms.
     *
     * @param string[] $taxonomies An array of taxonomy slugs to get terms for.
     * @return \WP_Term[] An array of WP_Term objects.
     * @see https://developer.wordpress.org/reference/classes/wp_term/
     */
    public function getTerms(array $taxonomies): array;

    /**
     * Get the post object image.
     *
     * @return ImageInterface|null The image associated with the post object, or null if not found.
     */
    public function getImage(): ?ImageInterface;

    /**
     * Retrieve a value by its key.
     *
     * @param string $key The key to retrieve the value for.
     * @return mixed The value associated with the key, or null if not found.
     */
    public function __get(string $key): mixed;
}
