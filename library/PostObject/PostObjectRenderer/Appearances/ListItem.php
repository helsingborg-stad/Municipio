<?php

namespace Municipio\PostObject\PostObjectRenderer\Appearances;

use Municipio\PostObject\PostObjectInterface;
use Municipio\PostObject\PostObjectRenderer\PostObjectRendererInterface;

/**
 * Render PostObject as a list item.
 */
class ListItem implements PostObjectRendererInterface
{
    /**
     * @inheritDoc
     */
    public function render(PostObjectInterface $postObject): string
    {
        return "<li><a href=\"{$postObject->getPermalink()}\">{$postObject->getTitle()}</a></li>";
    }
}
