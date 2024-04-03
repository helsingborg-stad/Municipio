<?php

namespace Municipio\Controller;

use Municipio\Helper\ContentType;

class ArchiveContentType extends \Municipio\Controller\Archive
{
    public function init()
    {
        parent::init();

        $this->data['displayArchiveLoop'] = (bool) ($this->data['archiveProps']->displayArchiveLoop ?? true);
    }
}
