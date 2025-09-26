<?php

namespace Municipio\Admin\Login;

use Municipio\Helper\User\Config\UserConfig;
use Municipio\Helper\User\Config\UserConfigInterface;
use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;


/**
 * Set default role if none exists on the current user
 */
class SetDefaultRoleIfNone implements Hookable
{
    /**
     * Constructor
     */
    public function __construct(private WpService $wpService, private UserConfigInterface $userConfig)
    {
    }

    /**
     * Add hooks
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('init', array($this, 'setDefaultRoleIfNoneDefined'));
    }

    /**
     * Set default role if none exists on the current user
     * @context multisite
     * 
     * @return void
     */
    public function setDefaultRoleIfNoneDefined(): void
    {
      if ($this->wpService->isUserLoggedIn() && $this->wpService->isMultisite()) {
        $user = $this->wpService->wpGetCurrentUser();
        if (empty($user->roles)) {
            $user->set_role($this->userConfig->getDefaultRole());
        }
      }
    }
}
