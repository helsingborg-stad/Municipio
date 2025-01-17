<?php

namespace Municipio\Integrations\MiniOrange;

use Municipio\Helper\User\Contracts\SetUserGroup;
use Municipio\HooksRegistrar\Hookable;
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
        private SetUserGroup $userHelper
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
        $groupName = $this->getGroupNameFromMixed($groupName);

        if (!$groupName || is_numeric($groupName)) {
            return;
        }

        $this->userHelper->setUserGroup($groupName, $userId);
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
