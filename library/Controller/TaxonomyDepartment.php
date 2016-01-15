<?php

namespace Municipio\Controller;

class TaxonomyDepartment extends \Municipio\Controller\BaseController
{
    public function init()
    {
        $this->data['term'] = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
    }
}
