<?php

namespace Municipio\Controller\ContentType\Traits;

trait AddSecondaryContentType
{
    protected $secondaryContentTypes = [];

    /**
     * Add a secondary content type.
     *
     * @param \Municipio\Controller\ContentType\ContentTypeFactory $contentType The content type to add.
     * @return void
     */
    public function addSecondaryContentType(
        \Municipio\Controller\ContentType\ContentTypeFactory $contentType
    ): void {
        if (\Municipio\Helper\ContentType::validateSimpleContentType($contentType, $this)) {
            $this->secondaryContentType[] = $contentType;
        }
    }
}
