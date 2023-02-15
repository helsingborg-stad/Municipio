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
        if (!empty($currentPurpose = PurposeHelper::getPurposes($type))) {
            if (!empty($currentPurpose['main'])) {
                $skipTemplate = PurposeHelper::skipPurposeTemplate($type);
                if (!$skipTemplate && !empty($currentPurpose['main']->view)) {
                    $this->view = $currentPurpose['main']->view;
                }
            }
        }
        // STRUCTURED DATA (SCHEMA.ORG)
        $this->data['structuredData'] = DataHelper::getStructuredData(
            $this->data['postType'],
            $this->getPageID()
        );
    }
}
