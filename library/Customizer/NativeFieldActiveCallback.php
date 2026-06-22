<?php

namespace Municipio\Customizer;

use Kirki\Util\Helper as KirkiHelper;

class NativeFieldActiveCallback
{
    /**
     * Translate Kirki active callbacks to native Customizer active callbacks.
     *
     * @param mixed $activeCallback Active callback from a field definition.
     *
     * @return callable|string|null
     */
    public static function fromField(mixed $activeCallback): callable|string|null
    {
        if ($activeCallback === null || is_callable($activeCallback)) {
            return $activeCallback;
        }

        if (!is_array($activeCallback)) {
            return null;
        }

        return static fn(): bool => self::conditionsMatch($activeCallback);
    }

    /**
     * Determine if any active callback condition matches.
     *
     * @param array $conditions Active callback conditions.
     *
     * @return bool
     */
    private static function conditionsMatch(array $conditions): bool
    {
        foreach ($conditions as $condition) {
            if (self::conditionMatches($condition)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a single active callback condition matches.
     *
     * @param mixed $condition Active callback condition.
     *
     * @return bool
     */
    private static function conditionMatches(mixed $condition): bool
    {
        if (!is_array($condition) || !is_string($condition['setting'] ?? null) || !is_string($condition['operator'] ?? null)) {
            return false;
        }

        return KirkiHelper::compare_values(
            get_theme_mod($condition['setting']),
            $condition['value'] ?? null,
            $condition['operator'],
        );
    }
}
