<?php

namespace Municipio\PostTypeDesign;

use Municipio\PostTypeDesign\SaveDesigns;

class PostTypeDesign {
    private string $optionName = 'post_type_design';

    public function __construct()
    {
        $saveDesignInstance = new SaveDesigns($this->optionName);
        $saveDesignInstance->addHooks();
        
        new SetDesigns($this->optionName);
    }
}