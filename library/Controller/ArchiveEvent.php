<?php

namespace Municipio\Controller;

use Municipio\Helper\Memoize\MemoizedFunction;
use Municipio\PostObject\PostObjectInterface;

/**
 * Class ArchiveEvent
 *
 * Handles archive for posts using the Event schema type.
 */
class ArchiveEvent extends \Municipio\Controller\Archive
{
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
        $this->data['getEventPriceRange'] = new MemoizedFunction(fn(PostObjectInterface $post) => ArchiveEvent\GetEventPriceRange::getEventPriceRange($getSchema($post)), fn(PostObjectInterface $post) => $post->getId());
        $this->data['getEventPlaceName']  = fn(PostObjectInterface $post) => ArchiveEvent\GetEventPlaceName::getEventPlaceName($getSchema($post));
        $this->data['getEventDate']       = fn(PostObjectInterface $post) => ArchiveEvent\GetEventDate::getEventDate($getSchema($post));
        $this->data['getDatebadgeDate']   = fn(PostObjectInterface $post) => ArchiveEvent\GetDatebadgeDate::getDatebadgeDate($getSchema($post));
    }
}
