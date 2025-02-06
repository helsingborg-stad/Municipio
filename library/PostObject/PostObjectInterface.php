<?php

namespace Municipio\PostObject;

use Municipio\PostObject\Icon\IconInterface;
use Municipio\PostObject\TermIcon\TermIconInterface;

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
}
