<?php

namespace Municipio\ExternalContent\Sync\Triggers;

interface InProgressFactoryInterface
{
    /**
     * Create a new in progress instance.
     *
     * @param string $postType
     * @return InProgressInterface
     */
    public function create(string $postType): InProgressInterface;
}
