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
    public $view;
    public function __construct()
    {
        parent::__construct();

        $type = $this->data['post']->postType;

        /**
         * Setup current purpose
         */
        $purpose = PurposeHelper::getPurpose($type);
        $instance = PurposeHelper::getPurposeInstance($purpose, true);

        if (!PurposeHelper::skipPurposeTemplate($type)) {
            $this->view = $instance->getView();
        }


        // STRUCTURED DATA (SCHEMA.ORG)
        $this->data['structuredData'] = DataHelper::getStructuredData(
            $this->data['postType'],
            $this->getPageID()
        );
    }
}
