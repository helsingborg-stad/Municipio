<?php

namespace Municipio\Customizer\Applicators;

use Municipio\Customizer\ActiveCallbackComparator;
use Municipio\Customizer\NativeField;
use Municipio\Customizer\PanelsRegistry;

abstract class AbstractApplicator
{
    public $optionKeyBasename = 'theme_mod_applicator_cache';
    public $optionKey = null;
    public $lastSignatureKey = 'theme_mod_last_signature';
    protected $signature = null;
    protected $runtimeCache = [];

    /**
     * Get fields.
     *
     * @return array
     */
    protected function getFields(): array
    {
        return $this->getRegisteredFields();
    }

    /**
     * Get field definitions registered outside the native field registry.
     *
     * @return array
     */
    private function getRegisteredFields(): array
    {
        $fields = [];

        foreach (PanelsRegistry::getInstance()->getRegisteredFields() as $field) {
            if (!is_array($field) || !isset($field['settings']) || !is_string($field['settings'])) {
                continue;
            }

            $fields[$field['settings']] = $field;
        }

        return $fields;
    }

    /**
     * Get the stored value for a field.
     *
     * @param string $settingKey The setting key to retrieve.
     * @param array  $field      The field definition.
     *
     * @return mixed
     */
    protected function getFieldValue(string $settingKey, array $field): mixed
    {
        if (($field[NativeField::FIELD_DRIVER_KEY] ?? null) === NativeField::FIELD_DRIVER) {
            return get_theme_mod($settingKey, $field['default'] ?? false);
        }

        return get_theme_mod($settingKey, $field['default'] ?? false);
    }

    /**
     * Compare values using Customizer active-callback operators.
     *
     * @param string $settingKey The setting key to compare.
     * @param mixed $value The value to compare.
     * @param string $operator The operator to use.
     *
     * @return boolean  True if the values match, false otherwise.
     */
    protected function compareValues($settingKey, $value, $operator): bool
    {
        $fields = $this->getFields();
        $settingKeyStoredValue = $this->getFieldValue($settingKey, $fields[$settingKey] ?? []);

        return ActiveCallbackComparator::compare($settingKeyStoredValue, $value, $operator);
    }

    /**
     * Check if a callback is valid, and if it should append data.
     *
     * @param array $activeCallback The active_callback part of the field.
     *
     * @return boolean
     */
    protected function isValidActiveCallback($activeCallback): bool
    {
        if (is_string($activeCallback) && $activeCallback === '__return_true') {
            return true;
        }

        if (is_string($activeCallback) && $activeCallback === '__return_false') {
            return false;
        }

        if (is_array($activeCallback) && !empty($activeCallback)) {
            $shouldReturn = false;
            foreach ($activeCallback as $cb) {
                $cb = (object) $cb;
                if ($this->compareValues($cb->setting, $cb->value, $cb->operator)) {
                    $shouldReturn = true;
                    break;
                }
            }
            return $shouldReturn;
        }

        return true;
    }

    /**
     * Determines if should be handled as a $type.
     *
     * @param array $field          The field to check.
     * @param string $lookForType   The type to look for.
     * @return boolean              True if the field should be handled as $type, false otherwise.
     */
    protected function isFieldType($field, $lookForType): bool
    {
        if (!$this->fieldHasOutput($field)) {
            return false;
        }

        foreach ($field['output'] as $output) {
            if ($this->fieldOutputHasMatchingType($output, $lookForType)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if field has output.
     *
     * @param array $field  The field to check.
     *
     * @return boolean
     */
    private function fieldHasOutput(array $field): bool
    {
        if (!isset($field['output']) || !is_array($field['output'])) {
            return false;
        }

        return !empty($field['output']);
    }

    /**
     * Determine if field output has matching type.
     *
     * @param array $output The output definition of a field.
     * @param string $type  The type to look for.
     *
     * @return boolean      True if the output has the matching type, false otherwise.
     */
    private function fieldOutputHasMatchingType(array $output, string $type): bool
    {
        if (!isset($output['type'])) {
            return false;
        }

        return $output['type'] === $type;
    }
}
