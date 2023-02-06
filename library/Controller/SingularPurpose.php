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

        // ! TODO Remove this when done
        require_once MUNICIPIO_PATH . 'library/Controller/Purpose/Project.php';
        $project = new \Municipio\Controller\Purpose\Project();
        // ! TODO
        // ! 1) Get purposes for the currently loaded type of content (post type or taxonomy)
        // ! 2) Iterate those and load each controller

        if (!empty($purposes = PurposeHelper::getRegisteredPurposes())) {
            foreach ($purposes as $purpose) {
                echo '<pre>' . print_r($purpose, true) . '</pre>';
            }
        }

        // STRUCTURED DATA (SCHEMA.ORG)
        $this->data['structuredData'] = DataHelper::getStructuredData(
            $this->data['postType'],
            $this->getPageID()
        );

        echo '<pre>' . print_r($this->data['structuredData'], true) . '</pre>';
    }
}
