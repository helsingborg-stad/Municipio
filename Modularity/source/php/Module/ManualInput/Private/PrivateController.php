<?php

namespace Modularity\Module\ManualInput\Private;

use Modularity\Module\ManualInput\ManualInput;

class PrivateController
{
    public static $index = 0;
    private string $userMetaKey = 'manualInputs';
    public function __construct(private ManualInput $manualInputInstance)
    {
        $this->registerMeta();
        add_filter('acf/update_value/key=field_6718c31e2862b', array($this, 'assignUniqueIdToRows'), 20, 4);

        add_filter('acf/prepare_field/key=field_678784f60a1a6', [$this, 'onlyShowCustomMetaKeyFieldIfAdministrator']);

        add_filter('acf/update_value/key=field_678784f60a1a6', [$this, 'checkForChangedMetaKeyValue'], 10, 4);

        // Do not cache private manual inputs
        if ($this->manualInputInstance->postStatus === 'private') {
            $this->manualInputInstance->cacheTtl = 0;
        }
    }

    public function decorateData(array $data, array $fields): array
    {
        if (
            $this->manualInputInstance->postStatus !== 'private' ||
            empty($fields['allow_user_modification'])
        ) {
            return $data;
        }

        $user = wp_get_current_user();

        if (empty($user->ID)) {
            return $data;
        }

        $data['template'] = $this->manualInputInstance->template;
        $this->manualInputInstance->template = 'private';

        $data['user'] = $user->ID;
        $data['userMetaKey'] = $this->userMetaKey;
        $data['privateModuleMetaKey'] = $this->getPrivateMetaKey($fields);

        $data['lang'] = [
            'save'        => __('Save', 'modularity'),
            'cancel'      => __('Cancel', 'modularity'),
            'description' => __('Description', 'modularity'),
            'name'        => __('Name', 'modularity'),
            'saving'      => __('Saving', 'modularity'),
            'obligatory'  => __('This item is obligatory', 'modularity'),
            'error'       => __('An error occurred and the data could not be saved. Please try again later', 'modularity'),
            'changeContent' => __('Change the lists content', 'modularity'),
        ];

        $data['filteredManualInputs'] = $this->getUserStructuredManualInputs($data, $user->ID);

        return $data;
    }

    /**
     * Retrieves the private meta key for the module.
     *
     * This function takes an array of fields and checks if the 'save_as_custom_meta_key' field is not empty.
     * If it is not empty, it sanitizes the value and assigns it to the $privateModuleMetaKey variable.
     * If it is empty, it assigns the ID of the manualInputInstance to the $privateModuleMetaKey variable.
     *
     * @param array $fields The array of fields.
     * @return string The private meta key for the module.
     */
    private function getPrivateMetaKey(array $fields): string
    {
        $privateModuleMetaKey = null;

        if (!empty($fields['save_as_custom_meta_key'])) {
            $privateModuleMetaKey = sanitize_title($fields['save_as_custom_meta_key']);
        }

        return !empty($privateModuleMetaKey) ? $privateModuleMetaKey : $this->manualInputInstance->ID;
    }

    private function getUserStructuredManualInputs(array $data): array
    {
        $userManualInputs = get_user_meta($data['user'], 'manualInputs', true);
        $userManualInput = $userManualInputs[$this->manualInputInstance->ID] ?? null;

        $filteredManualInputs = [];
        foreach ($data['manualInputs'] as $manualInput) {
            $manualInput['classList'] ??= [];
            $manualInput['attributeList'] ??= [];

            if (
                empty($manualInput['obligatory']) && 
                isset($userManualInput[$manualInput['uniqueId']]) && 
                !$userManualInput[$manualInput['uniqueId']]
            ) {
                $manualInput['classList'][] = 'u-display--none';
                $manualInput['checked'] = false;
            } else {
                $manualInput['checked'] = true;
            }

            $manualInput['attributeList']['data-js-item-id'] = $manualInput['uniqueId']; 

            $filteredManualInputs[] = $manualInput;
        }

        return $filteredManualInputs;
    }

    private function registerMeta(): void
    {
        register_meta('user', 'manualInputs', array(
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

    public function assignUniqueIdToRows($value, $postId, $field, $original): string
    {
        if (empty($value)) {
            $value = self::$index . '-' . uniqid();
            self::$index++;
        }

        return $value;
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