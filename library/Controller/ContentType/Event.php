<?php

namespace Municipio\Controller\ContentType;

use Municipio\Helper\ContentType as ContentTypeHelper;

/**
 * Class Event
 *
 * Used to represent events such as concerts, exhibitions, etc.
 *
 * @package Municipio\Controller\ContentType
 */
class Event extends ContentTypeFactory implements ContentTypeComplexInterface
{
    public function __construct()
    {
        $this->key = 'event';
        $this->label = __('Event', 'municipio');

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
        if (ContentTypeHelper::validateSimpleContentType($contentType, $this)) {
            $this->secondaryContentType[] = $contentType;
        }
    }
    
}
