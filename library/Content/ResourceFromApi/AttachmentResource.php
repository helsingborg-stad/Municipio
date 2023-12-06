<?php

namespace Municipio\Content\ResourceFromApi;

class AttachmentResource extends PostTypeResource
{
    public function getType(): string
    {
        return ResourceType::ATTACHMENT;
    }
}
