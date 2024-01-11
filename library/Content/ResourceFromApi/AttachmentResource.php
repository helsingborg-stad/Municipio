<?php

namespace Municipio\Content\ResourceFromApi;

/**
 * Attachment resource.
 */
class AttachmentResource extends PostTypeResource
{
    /**
     * Returns the type of the resource.
     *
     * @return string The type of the resource.
     */
    public function getType(): string
    {
        return ResourceType::ATTACHMENT;
    }
}
