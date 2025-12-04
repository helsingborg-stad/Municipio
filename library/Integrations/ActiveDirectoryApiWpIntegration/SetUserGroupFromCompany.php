<?php

namespace Municipio\Integrations\ActiveDirectoryApiWpIntegration;

use Municipio\Helper\User\Contracts\SetUserGroup;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\{AddAction, GetTermBy, IsWpError, WpInsertTerm, WpSetObjectTerms};

/**
 * Set group as taxonomy
 */
class SetUserGroupFromCompany implements Hookable
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
        $this->wpService->addAction('adApiWpIntegration/profile/updated', array($this, 'setUserGroupFromCompany'), 10, 3);
    }

    /**
     * Set the group as a taxonomy
     *
     * @param int $userId
     * @param string|array $groupName
     * @return void
     */
    public function setUserGroupFromCompany(int $userId, string|object $data, array $fields): void
    {
        $groupName = $this->getGroupName($fields);
        if(is_null($groupName)) {
            return;
        }
        $this->userHelper->setUserGroup($groupName, $userId);
    }

    /**
     * Set the group as a taxonomy
     *
     * @param string|array $data
     * @return string|null $groupName
     */
    private function getGroupName(mixed $data): ?string
    {
        if (empty($data) || !is_array($data)) {
            return null;
        }

        foreach(['companyname', 'company'] as $field) {
            if (is_array($data) && array_key_exists($field, $data) && !empty($data[$field])) {
                return $data[$field];
            }
        }
        
        return null;
    }
}
