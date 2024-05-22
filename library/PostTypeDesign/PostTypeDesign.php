<?php

namespace Municipio\PostTypeDesign;

use Municipio\PostTypeDesign\SaveDesigns;

class PostTypeDesign {
    private string $optionName = 'post_type_design';

    public function __construct()
    {
        new SaveDesigns($this->optionName);
        new SetDesigns($this->optionName);
    }
}