<?php

namespace Municipio\Customizer;

use Municipio\HooksRegistrar\Hookable;
use WP_User;
use WpService\Contracts\AddFilter;

/**
 * Allows selected local roles to edit WordPress custom CSS in multisite.
 */
class CustomCssCapabilities implements Hookable
{
    /**
     * @var array<int, string>
     */
    private const ALLOWED_ROLES = [
        'administrator',
        'editor',
    ];

    /**
     * Class constructor.
     */
    public function __construct(private AddFilter $wpService)
    {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter('map_meta_cap', [$this, 'allowCustomCssEditing'], 10, 4);
    }

    /**
     * Allow administrators and editors to pass the Custom CSS capability check.
     *
     * @param array<int, string> $requiredCapabilities
     * @param string $capability
     * @param int $userId
     * @param mixed ...$args
     *
     * @return array<int, string>
     */
    public function allowCustomCssEditing(
        array $requiredCapabilities,
        string $capability,
        int $userId,
        mixed ...$args
    ): array {
        if ($capability !== 'edit_css' || !$this->userHasAllowedRole($userId)) {
            return $requiredCapabilities;
        }

        return ['edit_theme_options'];
    }

    /**
     * Check if the user has a role allowed to edit custom CSS.
     */
    private function userHasAllowedRole(int $userId): bool
    {
        $user = get_userdata($userId);

        if (!$user instanceof WP_User) {
            return false;
        }

        return array_intersect(self::ALLOWED_ROLES, (array) $user->roles) !== [];
    }
}