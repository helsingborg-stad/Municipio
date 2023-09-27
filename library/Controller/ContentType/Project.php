<?php

namespace Municipio\Controller\ContentType;

/**
 * Class Project
 * @package Municipio\Controller\ContentType
 */
class Project extends ContentTypeFactory implements ContentTypeComplexInterface
{
    public function __construct()
    {
        $this->key = 'project';
        $this->label = __('Project', 'municipio');

        parent::__construct($this->key, $this->label);

        $this->addSecondaryContentType(new Place());

    }

    /**
     * addSecondaryContentType
     *
     * @param ContentTypeComponentInterface $contentType
     * @return void
     */
    public function addSecondaryContentType(ContentTypeComponentInterface $contentType): void
    {
        $this->secondaryContentType[] = $contentType;
    }
    
}
