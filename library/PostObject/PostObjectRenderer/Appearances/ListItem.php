<?php

namespace Municipio\PostObject\PostObjectRenderer\Appearances;

use Municipio\PostObject\PostObjectRenderer\PostObjectRendererInterface;

class ListItem extends PostObjectBladeRenderer implements PostObjectRendererInterface
{
    public function getViewName(): string
    {
        return 'ListItem';
    }
}
