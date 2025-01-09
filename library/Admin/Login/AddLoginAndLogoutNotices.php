<?php

namespace Municipio\Admin\Login;

use AcfService\AcfService;
use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;
use WP_Term;

class AddLoginAndLogoutNotices implements Hookable
{
  public function __construct(private WpService $wpService, private AcfService $acfService){}

  /**
   * Add hooks
   */
  public function addHooks(): void
  {
    $this->wpService->addAction('init', array($this, 'addNoticeWhenUserLogsIn'));
    $this->wpService->addAction('init', array($this, 'addNoticeWhenUserLogsOut'));
  }

  /**
   * Add notice when user logs in
   */
  public function addNoticeWhenUserLogsIn()
  {
    if ((bool)($_GET['loggedin'] ?? false) && $this->wpService->isUserLoggedIn()) {

      $currentUserGroup = $this->getCurrentUserGroup();

      $currentUserGroupUrl = $this->getCurrentUserGroupUrl(
          $currentUserGroup
      );

      if($currentUserGroupUrl){
        \Municipio\Helper\Notice::add(__('Login successful', 'municipio'), 'info', 'login', [
          'url' => $currentUserGroupUrl,
          'text' => __('Go to', 'municipio') . ' ' . $currentUserGroup->name ?? __('home', 'municipio')
        ]);
      } else {
        \Municipio\Helper\Notice::add(__('Login successful', 'municipio'), 'info', 'login');
      }
    }
  }

  /**
   * Add notice when user logs out
   */
  public function addNoticeWhenUserLogsOut()
  {
    if ((bool)($_GET['loggedout'] ?? false) && !$this->wpService->isUserLoggedIn()) {
      \Municipio\Helper\Notice::add(__('Logout successful', 'municipio'), 'info', 'logout');
    }
  }

 /**
   * Get current user group
   *
   * @return string|null
   */
  private function getCurrentUserGroup(): ?WP_Term
  {
      //Check login
      $user = $this->wpService->wpGetCurrentUser();
      if (!$user) {
          return null;
      }

      //Check if user has a group
      $userGroup = $this->wpService->wpGetObjectTerms($user->ID, 'user_group');
      if (empty($userGroup) || $this->wpService->isWpError($userGroup)) {
          return null;
      }

      //Only get first item
      if (is_array($userGroup)) {
          $userGroup = array_shift($userGroup);
      }

      return is_a($userGroup, 'WP_Term') ? $userGroup : null;
  }

  /**
   * Get the current user group URL
   *
   * @param WP_Term|null $term
   * @return string
   */
  private function getCurrentUserGroupUrl(?WP_Term $term): ?string
  {
      // Ensure term exists
      if (!$term) {
          return null;
      }

      // Create the term ID
      $termId = 'user_group_' . $term->term_id;

      // Get the selected type of link
      $typeOfLink = $this->acfService->getField('user_group_type_of_link', $termId);

      // Return null if the option is disabled
      if ($typeOfLink === 'disabled') {
          return null;
      }

      // Handle arbitrary URL
      if ($typeOfLink === 'arbitrary_url') {
          return $this->acfService->getField('arbitrary_url', $termId) ?: null;
      }

      // Handle post type
      if ($typeOfLink === 'post_type') {
          $postObject = $this->acfService->getField('post_type', $termId);
          if ($postObject && isset($postObject->ID)) {
              return get_permalink($postObject->ID);
          }
          return null;
      }

      // Handle blog ID in multisite
      if ($typeOfLink === 'blog_id') {
          $blogId = $this->acfService->getField('blog_id', $termId);
          if ($blogId) {
              $blogDetails = $this->wpService->getBlogDetails($blogId);
              return (function (?object $details): ?string {
                  return $details ? '//' . $details->domain . $details->path : null;
              })($blogDetails);
          }
          return null;
      }

      // Default case (should not occur)
      return null;
  }

}