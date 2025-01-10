<?php

namespace Municipio\Helper;

use WP_Term;

class User
{
    /**
     * Check if current user has a specific role
     * Can also check multiple roles, returns true if any of exists for the user
     * @param  string|array  $roles Role or roles to check
     * @return boolean
     */
    public static function hasRole($roles)
    {
        $user = wp_get_current_user();

        if (is_string($roles)) {
            $roles = array($roles);
        }

        if (!array_intersect($roles, $user->roles)) {
            return false;
        }

        return true;
    }

    /**
     * Get current user group
     *
     * @return string|null
     */
    public static function getCurrentUserGroup(): ?WP_Term
    {
        //Init services
        $wpService = \Municipio\Helper\WpService::get();

        //Check login
        if (!$wpService->isUserLoggedIn()) {
            return null;
        }

        //Get user
        $user = $wpService->wpGetCurrentUser();

        //Check if user has a group
        $userGroup = $wpService->wpGetObjectTerms($user->ID, 'user_group');
        if (empty($userGroup) || $wpService->isWpError($userGroup)) {
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
    public static function getCurrentUserGroupUrl(?WP_Term $term): ?string
    {
        //Init services
        $wpService  = \Municipio\Helper\WpService::get();
        $acfService = \Municipio\Helper\AcfService::get();

        // Get the current user group
        if ($term === null) {
            $term = self::getCurrentUserGroup();
        }

        // Ensure term exists
        if (!$term) {
            return null;
        }

        // Create the term ID
        $termId = 'user_group_' . $term->term_id;

        // Get the selected type of link
        $typeOfLink = $acfService->getField('user_group_type_of_link', $termId);

        // Return null if the option is disabled
        if ($typeOfLink === 'disabled') {
            return null;
        }

        // Handle arbitrary URL
        if ($typeOfLink === 'arbitrary_url') {
            return $acfService->getField('arbitrary_url', $termId) ?: null;
        }

        // Handle post type
        if ($typeOfLink === 'post_type') {
            $postObject = $acfService->getField('post_type', $termId);
            if ($postObject && isset($postObject->ID)) {
                return get_permalink($postObject->ID);
            }
            return null;
        }

        // Handle blog ID in multisite
        if ($typeOfLink === 'blog_id') {
            $blogId = $acfService->getField('blog_id', $termId);
            if ($blogId) {
                $blogDetails = $wpService->getBlogDetails($blogId);
                return (function (?object $details): ?string {
                    return $details ? '//' . $details->domain . $details->path : null;
                })($blogDetails);
            }
            return null;
        }

        // Default case (should not occur)
        return null;
    }

    /**
     * Get the user prefers group URL.
     * This indicates that the user prefers to be 
     * redirected to the group URL after login.
     *
     * @return bool
     */
    public static function getUserPrefersGroupUrl(): bool
    {
        $wpService = \Municipio\Helper\WpService::get();

        $perfersGroupUrl = $wpService->getUserMeta(
            $wpService->wpGetCurrentUser()->ID, 
            self::getUserPrefersGroupUrlMetaKey(), 
            false
        );
        if ($perfersGroupUrl === 'true') {
            return true;
        }
        return false;
    }

    public static function getUserPrefersGroupUrlMetaKey(): string
    {
        return 'user_prefers_group_url';
    }
}
