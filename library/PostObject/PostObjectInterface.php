<?php

namespace Municipio\PostObject;

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
     * Get the term icons.
     *
     * @return \Municipio\PostObject\TermIcon\TermIconInterface[]
     */
    public function getTermIcons(): array;

    /**
     * Get the post type.
     */
    public function getPostType(): string;
}
