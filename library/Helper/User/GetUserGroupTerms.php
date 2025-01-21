<?php

namespace Municipio\Helper\User;

use WpService\Contracts\GetTerms;
use WpService\Contracts\IsMainSite;
use WpService\Contracts\IsMultisite;
use Municipio\Helper\SiteSwitcher\SiteSwitcher;
use WpService\Contracts\GetMainSiteId;

/**
 * Class GetUserGroupTerms
 *
 * This class is responsible for retrieving the group terms associated with a user.
 */
class GetUserGroupTerms
{
    private ?array $userGroupTaxonomyTerms = null;

    /**
     * Constructor for the GetUserGroupTerms class.
     */
    public function __construct(
        private GetTerms&IsMainSite&IsMultisite&GetMainSiteId $wpService,
        private string $userGroupTaxonomyName,
        private SiteSwitcher $siteSwitcher
    ) {
    }

    /**
     * Retrieves the user group terms.
     *
     * This method retrieves the user group terms. If the site is a multisite and not the main site,
     * it will switch to the main site to retrieve the terms.
     *
     * @return array The user group terms.
     */
    public function get(): array
    {
        if (!is_null($this->userGroupTaxonomyTerms)) {
            return $this->userGroupTaxonomyTerms;
        }

        if ($this->wpService->isMultisite() && !$this->wpService->isMainSite()) {
            $this->userGroupTaxonomyTerms = $this->siteSwitcher->runInSite(
                $this->wpService->getMainSiteId(),
                function () {
                    return $this->getTerms();
                }
            );
        } else {
            $this->userGroupTaxonomyTerms = $this->getTerms();
        }

        return $this->userGroupTaxonomyTerms;
    }

    /**
     * Retrieves the user group terms.
     *
     * This method retrieves the user group terms.
     *
     * @return array The user group terms.
     */
    private function getTerms()
    {
        $userGroupTerms = $this->wpService->getTerms([
            'taxonomy'   => $this->userGroupTaxonomyName,
            'hide_empty' => false,
        ]);

        return $userGroupTerms ?: [];
    }
}
