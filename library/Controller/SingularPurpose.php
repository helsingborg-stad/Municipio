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
         * Load and instantiate the current purpose
         */
        if (!empty($currentPurpose = PurposeHelper::getPurposes($type))) {
            if (!empty($currentPurpose['main'])) {
                $purpose = $currentPurpose['main'];
                $purpose->init();

                $skipTemplate = PurposeHelper::skipPurposeTemplate($type);
                // This will need to be refactored if we decide to allow multiple purposes on a single type
                if (!$skipTemplate && !empty($purpose->view)) {
                    $this->view = $purpose->view;
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
