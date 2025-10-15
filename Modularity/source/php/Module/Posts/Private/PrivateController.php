<?php

namespace Modularity\Module\Posts\Private;

use Modularity\Module\Posts\Posts;

class PrivateController {
    private string $userMetaKey = 'privatePostsModule';

    public function __construct(private Posts $postsInstance)
    {
        add_filter('acf/prepare_field/key=field_678665cc4cc6e', [$this, 'onlyShowCustomMetaKeyFieldIfAdministrator']);

        add_filter('acf/update_value/key=field_678665cc4cc6e', [$this, 'checkForChangedMetaKeyValue'], 10, 4);

        $this->registerMeta();

        if ($this->postsInstance->postStatus === 'private') {
            $this->postsInstance->cacheTtl = 0;
            add_filter(
                'Modularity/Module/Posts/template', 
                array($this, 'checkIfModuleCanBeEditedByUser'), 
                999, 
                4
            );
        }
    }

    public function checkIfModuleCanBeEditedByUser($view, $instance, $data, $fields)
    {
        if (!$this->allowsUserModification($fields)) {
            return $view;
        }

        return 'private.blade.php';
    }

    public function decorateData(array $data, array $fields): array
    {
        if (!$this->allowsUserModification($fields)) {
            return $data;
        }
        
        $user = wp_get_current_user();
        $data['userMetaKey']          = $this->userMetaKey;
        $data['currentUser']          = $user->ID;
        $data['privateModuleMetaKey'] = $this->getPrivateMetaKey($fields);
        $data['userCanEditPosts']     = true;
        $data['filteredPosts']        = $this->getUserStructuredPosts(
            $data['posts'], 
            $data['currentUser'], 
            $data['privateModuleMetaKey']
        );

        return $data;
    }

    private function getPrivateMetaKey(array $fields): string
    {
        $privateModuleMetaKey = null;

        if (!empty($fields['save_as_custom_meta_key'])) {
            $privateModuleMetaKey = sanitize_title($fields['save_as_custom_meta_key']);
        }

        return !empty($privateModuleMetaKey) ? $privateModuleMetaKey : $this->postsInstance->ID;
    }

    private function getUserStructuredPosts(array $posts, int $currentUser, string $privateModuleMetaKey): array
    {
        $userPosts = get_user_meta($currentUser, $this->userMetaKey, true);

        foreach ($posts as &$post) {
            $post->classList = $post->classList ?? [];

            if (
                !empty($userPosts) &&
                !empty($userPosts[$privateModuleMetaKey]) &&
                isset($userPosts[$privateModuleMetaKey][$post->getId()]) && 
                empty($userPosts[$privateModuleMetaKey][$post->getId()])
            ) {
                $post->checked     = false;
                $post->classList[] = 'u-display--none';
            } else {
                $post->checked     = true;
            }
        }

        return $posts;
    }

    private function allowsUserModification(array $fields): bool
    {
        return 
            $this->postsInstance->postStatus === 'private' && 
            !empty($fields['allow_user_modification']) && 
            is_user_logged_in();
    }

    private function registerMeta(): void
    {
        register_meta('user', $this->userMetaKey, array(
            'type' => 'object',
            'show_in_rest' => array(
                'schema' => array(
                    'type' => 'object',
                    'additionalProperties' => array(
                        'type' => 'object',
                        'properties' => array(
                            'key' => array(
                                'type' => 'bool',
                            ),
                        ),
                        'additionalProperties' => true,
                    ),
                ),
            ),
            'single' => true,
        ));
    }

    /**
     * Checks if the meta key value has changed and updates the user meta accordingly.
     *
     * @param mixed $value The new value of the meta key.
     * @param int $postId The ID of the post.
     * @param array $field The field array containing the key.
     * @param mixed $originalValue The original value of the meta key.
     * @return mixed The updated value of the meta key.
     */
    public function checkForChangedMetaKeyValue($value, $postId, $field, $originalValue) 
    {
        $oldKey = get_field($field['key'], $postId);
        $oldKey = sanitize_title(empty($oldKey) ? $postId : $oldKey);

        $newKey = sanitize_title(empty($value) ? $postId : $value);

        if ($oldKey === $newKey) {
            return $value;
        }

        $user = wp_get_current_user();

        $userMeta = get_user_meta($user->ID, $this->userMetaKey, true);

        if (isset($userMeta[$oldKey])) {
            $userMeta[$newKey] = $userMeta[$oldKey];
            unset($userMeta[$oldKey]);

            update_user_meta($user->ID, $this->userMetaKey, $userMeta);
        }

        return $value;
    }

    /**
     * Determines if the custom meta key field should be shown only for administrators.
     *
     * @param mixed $field The field to be checked.
     *
     * @return mixed Returns the field if the current user is an administrator, otherwise returns false.
     */
    public function onlyShowCustomMetaKeyFieldIfAdministrator($field)
    {
        $user = wp_get_current_user();

        if (!$user->caps || !in_array('administrator', $user->caps)) {
            $field['wrapper']['class'] = 'acf-hidden';
        }
        
        return $field;
    }
}