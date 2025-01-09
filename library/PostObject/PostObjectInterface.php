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
     * Get the term icons.
     *
     * @return \Municipio\PostObject\TermIcon\TermIconInterface[]
     */
    public function getTermIcons(): array;

    /**
     * Get the term icon.
     * The first term icon found.
     *
     * @param string|null $taxonomy Optional taxonomy to get the term icon from. If null, the first term icon found will be returned regardless of taxonomy.
     * @return \Municipio\PostObject\TermIcon\TermIconInterface|null The first term icon found or null if none is found.
     */
    public function getTermIcon(?string $taxonomy = null): ?TermIconInterface;

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
}
