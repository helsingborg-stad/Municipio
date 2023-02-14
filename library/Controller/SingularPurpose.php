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

        $availablePurposes = PurposeHelper::getRegisteredPurposes(true);
        if (!empty($currentPurposes = PurposeHelper::getPurposes($type))) {
            foreach ($currentPurposes as $purpose) {
                if (is_file($availablePurposes[$purpose]['path'])) {
                    require_once $availablePurposes[$purpose]['path'];

                    $purposeObject = new $availablePurposes[$purpose]['class']();
                    $purposeObject->init();

                    $skipTemplate = PurposeHelper::skipPurposeTemplate($type);
                    // This will need to be refactored if we decide to allow multiple purposes on a single type
                    if (!$skipTemplate && !empty($purposeObject->view)) {
                        $this->view = $purposeObject->view;
                    }
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
