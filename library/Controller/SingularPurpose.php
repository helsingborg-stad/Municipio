<?php

namespace Municipio\Controller;

use Municipio\Helper\Data as DataHelper;
use Municipio\Helper\Purpose as PurposeHelper;

/**
 * Class SingularPurpose
 * @package Municipio\Controller
 */
class SingularPurpose extends \Municipio\Controller\Singular
{
    public function __construct()
    {
        parent::__construct();

        // STRUCTURED SCHEMA.ORG DATA
        $this->data['structuredData'] = DataHelper::getStructuredData(
            $this->data['postType'],
            $this->getPageID()
        );
    }
}
