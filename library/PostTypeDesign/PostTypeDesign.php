<?php

namespace Municipio\PostTypeDesign;

use Municipio\PostTypeDesign\SaveDesigns;

class PostTypeDesign {
    public function __construct()
    {
        new SaveDesigns();
    }
}