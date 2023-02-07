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

        /**
         * Load and instantiate the purposes registered in the system.
         *
         * @return void
         */
        $availablePurposes = PurposeHelper::getRegisteredPurposes(true);
        if (!empty($currentPurposes = PurposeHelper::getPurposes())) {
            foreach ($currentPurposes as $purpose) {
                if (is_file($availablePurposes[$purpose]['path'])) {
                    require_once $availablePurposes[$purpose]['path'];

                    $purposeObject = new $availablePurposes[$purpose]['class']();
                    $purposeObject->init();
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
