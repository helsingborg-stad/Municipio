<?php

namespace Municipio\Helper\Constant;

/**
 * Interface ConstantInterface
 *
 * Provides methods to check if a constant is defined and to retrieve its value.
 * This interface is useful for abstracting the access to constants, allowing for easier testing and flexibility.
 */
interface ConstantInterface {
    /**
     * Checks if a constant is defined.
     * 
     * @param string $name The name of the constant.
     * @return bool True if the constant is defined, false otherwise.
     * @see https://www.php.net/manual/en/function.defined.php
     */
    public function defined(string $name): bool;

    /**
     * Retrieves the value of a constant.
     * 
     * @param string $name The name of the constant.
     * @return mixed The value of the constant.
     * @see https://www.php.net/manual/en/function.constant.php
     */
    public function constant(string $name): mixed;
}