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
        $this->data['getEventPlaceName']  = fn(PostObjectInterface $post) => ArchiveEvent\GetEventPlaceName::getEventPlaceName($post);
        $this->data['getEventDate']       = fn(PostObjectInterface $post) => ArchiveEvent\GetEventDate::getEventDate($post);
        $this->data['getDatebadgeDate']   = fn(PostObjectInterface $post) => ArchiveEvent\GetDatebadgeDate::getDatebadgeDate($post);
        $this->data['getEventPriceRange'] = new MemoizedFunction(fn(PostObjectInterface $post) => ArchiveEvent\GetEventPriceRange::getEventPriceRange($post), fn(PostObjectInterface $post) => $post->getId());
    }
}
