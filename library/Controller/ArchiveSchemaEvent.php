<?php

namespace Municipio\Controller;

use Municipio\Helper\Memoize\MemoizedFunction;
use Municipio\PostObject\PostObjectInterface;

/**
 * Class ArchiveSchemaEvent
 *
 * Handles archive for posts using the Event schema type.
 */
class ArchiveSchemaEvent extends \Municipio\Controller\Archive
{
    public string $view = 'archive-schema-event';   

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        /**
         * Make functions available to view through the data array.
         * Functions that risk getting called multiple times are memoized.
         */
        $getSchema                        = new MemoizedFunction(fn(PostObjectInterface $post) => $post->getSchema(), fn(PostObjectInterface $post) => $post->getId());
        $this->data['getEventPriceRange'] = new MemoizedFunction(fn(PostObjectInterface $post) => ArchiveSchemaEvent\GetEventPriceRange::getEventPriceRange($getSchema($post)), fn(PostObjectInterface $post) => $post->getId());
        $this->data['getEventPlaceName']  = fn(PostObjectInterface $post) => ArchiveSchemaEvent\GetEventPlaceName::getEventPlaceName($getSchema($post));
        $this->data['getEventDate']       = fn(PostObjectInterface $post) => ArchiveSchemaEvent\GetEventDate::getEventDate($getSchema($post));
        $this->data['getDatebadgeDate']   = fn(PostObjectInterface $post) => ArchiveSchemaEvent\GetDatebadgeDate::getDatebadgeDate($getSchema($post));
    }
}
