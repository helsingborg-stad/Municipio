<?php

declare(strict_types=1);

namespace Modularity\Helper\FragmentCache;

use Municipio\Helper\User\User;

/**
 * Identifies a fragment by everything that alters its output: the module input,
 * the capabilities of a logged-in user and request data such as query vars.
 */
final class FragmentHash
{
    /**
     * @param  mixed $module     Input data altering the output, as a string, array or object
     * @param  mixed $cacheGroup Request data altering the output, or null when there is none
     * @return string|false      False if the hash could not be created
     */
    public function create(mixed $module, mixed $cacheGroup): string|false
    {
        $hash = $this->createShortHash($module);

        if ($hash === false) {
            return false;
        }

        if (is_user_logged_in()) {
            $hash .= '-auth-' . $this->createShortHash($this->getUserCapabilities());
        }

        if ($cacheGroup !== null) {
            $hash .= '-group-' . $this->createShortHash($cacheGroup);
        }

        return $hash;
    }

    /**
     * @return array<string, mixed>
     */
    private function getUserCapabilities(): array
    {
        $caps = wp_get_current_user()->caps ?? [];
        $caps['superadmin'] = is_super_admin();
        $caps['usergroup'] = User::get()->getUserGroup()->term_id ?? null;

        return $caps;
    }

    /**
     * A fast, non-cryptographic hash. It only identifies cache entries.
     */
    private function createShortHash(mixed $input): string|false
    {
        try {
            $value = is_array($input) || is_object($input) ? serialize($input) : (string) $input;

            return substr(hash('xxh128', $value), 0, 16);
        } catch (\Exception $e) {
            return false;
        }
    }
}
