<?php

namespace Municipio\Integrations\MiniOrange;

use Municipio\Helper\Term\Contracts\CreateOrGetTermIdFromString;
use Municipio\HooksRegistrar\Hookable;
use Municipio\UserGroup\Config\UserGroupConfigInterface;
use WpService\Contracts\{AddAction, GetTermBy, IsWpError, WpInsertTerm, WpSetObjectTerms};

/**
 * Set group as taxonomy
 */
class SetUserGroupFromSsoLoginGroup implements Hookable
{
    /**
     * Constructor.
     */
    public function __construct(
        private AddAction&WpSetObjectTerms&GetTermBy&WpInsertTerm&IsWpError $wpService,
        private CreateOrGetTermIdFromString $termHelper,
        private UserGroupConfigInterface $config
    ) {
    }

    /**
     * Add hooks to map the attributes from the identified provider
     *
     * @return void
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('mo_saml_user_group_name', array($this, 'setUserGroupFromSsoLoginGroup'), 10, 2);
    }

    /**
     * Set the group as a taxonomy
     *
     * @param int $userId
     * @param string|array $groupName
     * @return void
     */
    public function setUserGroupFromSsoLoginGroup(int $userId, string|array $groupName): void
    {
        $taxonomy  = $this->config->getUserGroupTaxonomy();
        $groupName = $this->getGroupNameFromMixed($groupName);

        if (!$groupName || is_numeric($groupName)) {
            return;
        }

        if (!$userId) {
            return;
        }

        if ($termId = $this->termHelper->createOrGetTermIdFromString($groupName, $taxonomy)) {
            $this->wpService->wpSetObjectTerms($userId, $termId, $taxonomy, false);
        }
    }

    /**
     * Set the group as a taxonomy
     *
     * @param string|array $groupName
     * @return string|null $groupName
     */
    private function getGroupNameFromMixed(mixed $groupName): ?string
    {
        if (empty($groupName)) {
            return null;
        }
        if (is_array($groupName)) {
            $groupName = $groupName[0] ?? null;
        }
        return $groupName;
    }
}
