<?php

namespace Municipio\Helper;

class StyleguideCacheBust extends ManifestCacheBust
{
    public function __construct()
    {
        parent::__construct('/assets/dist/styleguide/manifest.json', get_template_directory());
    }
}
