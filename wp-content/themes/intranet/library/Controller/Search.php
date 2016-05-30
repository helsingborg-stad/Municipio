<?php

namespace Intranet\Controller;

class Search extends \Municipio\Controller\BaseController
{
    public $results = array();

    public function __construct()
    {
        global $searchwp;

        // Store the results from the current site
        $this->results[get_current_blog_id()] = $searchwp->results_weights;

        // Get results for the other sites
        $this->multisiteSearchWP();
    }

    public function multisiteSearchWP()
    {
        global $searchwp;
        $sites = \Intranet\Helper\Multisite::getSitesList(false, true);

        foreach ($sites as $siteId) {
            switch_to_blog($siteId);

            $searchwp->search('default', $searchwp->diagnostics[0]['terms']);
            $this->results[$siteId] = $searchwp->results_weights;

            restore_current_blog();
        }
    }
}
