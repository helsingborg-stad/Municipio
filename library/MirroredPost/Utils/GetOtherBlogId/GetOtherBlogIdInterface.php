<?php

namespace Municipio\MirroredPost\Utils\GetOtherBlogId;

interface GetOtherBlogIdInterface
{
    /**
     * Get the ID of the other blog.
     *
     * @return int|null The ID of the other blog, or null if not applicable.
     */
    public function getOtherBlogId(): ?int;
}
