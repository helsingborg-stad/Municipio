<?php

namespace Municipio\Customizer\Applicators;

use Kirki\Compatibility\Kirki as KirkiCompatibility;
use Kirki\Util\Helper as KirkiHelper;

abstract class AbstractApplicator
{
    public $optionKeyBasename = "theme_mod_applicator_cache";
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
        $fields = [];
		if (class_exists('\Kirki\Compatibility\Kirki') ) {
			$fields = array_merge(
                KirkiCompatibility::$fields ?? [], 
                KirkiCompatibility::$all_fields ?? [],
                $fields
            );
		}
        return $fields;
    }

    /**
     * Get static.
     * 
     * @return mixed
     */
    protected function getStatic() {
        
        if(is_null($this->signature)) {
            $this->signature = $this->getFieldSignature($this->getFields());
            $this->optionKey = sprintf('%s_%s_%s', $this->optionKeyBasename, $this->optionKey, $this->signature);
        }

        if(isset($this->runtimeCache[$this->optionKey])) {
            return $this->runtimeCache[$this->optionKey];
        }

        if(is_customize_preview()) {
            return false;
        }

        return $this->runtimeCache[$this->optionKey] = get_option($this->optionKey, false);
    }

    /**
     * Set static.
     * 
     * @param mixed $data The data to set.
     * 
     * @return boolean
     */
    protected function setStatic($data) {
        if(is_null($this->signature)) {
            $this->signature = $this->getFieldSignature($this->getFields());
            $this->optionKey = sprintf('%s_%s_%s', $this->optionKeyBasename, $this->optionKey, $this->signature);
        }

        update_option($this->lastSignatureKey, $this->signature);

        return update_option($this->optionKey, $data);
    }

    /**
     * Set runtime cache.
     * 
     * @param mixed $value The value to set.
     * 
     * @return mixed
     */
    protected function setRuntimeCache($identifier, $value)
    {
        $this->runtimeCache[
            md5($identifier)
        ] = $value;
        return $value;
    }

    /**
     * Get runtime cache.
     * 
     * @return mixed
     */
    protected function getRuntimeCache($identifier)
    {
        return $this->runtimeCache[
            md5($identifier)
        ] ?? null;
    }

    /**
     * Get storage key.
     * 
     * @param string $basename The basename to get the storage key for.
     * 
     * @return string
     */
    protected function getStorageKey($basename): string
    {
        return $basename . "_" . $this->getFieldSignature(
            $this->getFields()
        );
    }

    /**
     * Get field signature.
     * 
     * @param array $fields The fields to get the signature for.
     * 
     * @return string
     */
    protected function getFieldSignature($fields): string
    {
        $signature = get_option($this->lastSignatureKey, $this->getDefaultFieldSignature($fields));

        return $signature;
    }

    protected function getDefaultFieldSignature($fields): string
    {
        $supportedHashes = hash_algos() ?? [];
        if(in_array('xxh3', $supportedHashes)) {
            $hash = hash('sha256', json_encode($fields)); 
        }
        $hash = hash('md5', json_encode($fields)); 

        return $this->shortenHash($hash);
    }

    /**
     * Shorten hash.
     * 
     * @param string $hash The hash to shorten.
     * 
     * @return string
     */
    protected function shortenHash($hash): string
    {
        return substr($hash, 0, 8);
    }

    /**
     * Compare values using KirkiHelper.
     * 
     * @param string $settingKey The setting key to compare.
     * @param mixed $value The value to compare.
     * @param string $operator The operator to use.
     * 
     * @return boolean  True if the values match, false otherwise.
     */
    protected function compareValues($settingKey, $value, $operator): bool
    {
        $settingKeyStoredValue = \Kirki::get_option($settingKey);
        return KirkiHelper::compare_values(
            $settingKeyStoredValue,
            $value,
            $operator
        ); 
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
                if($this->compareValues($cb->setting, $cb->value,  $cb->operator)) {
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
