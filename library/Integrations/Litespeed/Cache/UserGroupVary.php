<?php

namespace Municipio\Integrations\Litespeed\Cache;

use Municipio\HooksRegistrar\Hookable;
use Municipio\Helper\User\User;
use WpService\Contracts\AddFilter;
use WpService\Contracts\IsUserLoggedIn;

/**
 * Class to add user group vary header for LiteSpeed Cache.
 */
class UserGroupVary implements Hookable
{

  public function __construct(public AddFilter&IsUserLoggedIn $wpService){}

  /**
   * Register hooks for adding user group vary header.
   * 
   * @return void
   */
  public function addHooks(): void
  {
  
    $this->wpService->addFilter(
      'litespeed_cache_vary_headers', 
      [$this, 'addUserGroupVaryHeader']
    );
  }

  /**
   * Add user group to vary headers for logged in users.
   * This ensures that different cached versions are served 
   * based on user groups.
   * 
   * @param array $varyHeaders
   * @return array
   */
  public function addUserGroupVaryHeader(array $varyHeaders): array
  {
    if(!$this->wpService->isUserLoggedIn()) {
      return $varyHeaders;
    }
    
    $userGroupId = User::get()->getUserGroup()->term_id ?? null;
    if($userGroupId !== null) {
      $varyHeaders['user_group'] = $userGroupId;
    }
    return $varyHeaders;
  }

}
