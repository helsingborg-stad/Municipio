<?php

namespace Municipio\PostObject;

use Municipio\PostObject\PostObjectRenderer\PostObjectRendererInterface;

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
     * Get the rendered post.
     *
     * @return string
     */
    public function getRendered(PostObjectRendererInterface $renderer): string;
}
