<?php

namespace Municipio\Template\Core;

class TaxonomyDepartment extends \Municipio\Template\CoreBase
{
    public function init()
    {
        \Bladerunner\Template::$data['term'] = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
    }
}
