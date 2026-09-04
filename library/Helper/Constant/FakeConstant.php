<?php

declare(strict_types=1);

namespace Municipio\Helper\Constant;

use Error;

/**
 * Provides in-memory constant values for tests.
 */
class FakeConstant implements ConstantInterface
{
    /**
     * @param array<string, mixed> $constants Constant names and their values.
     */
    public function __construct(private array $constants = [])
    {
    }

    /**
     * Check whether a constant value is available.
     */
    public function defined(string $name): bool
    {
        return array_key_exists($name, $this->constants);
    }

    /**
     * Retrieve a constant value.
     *
     * @throws Error If the constant is not defined.
     */
    public function constant(string $name): mixed
    {
        if (!$this->defined($name)) {
            throw new Error(sprintf('Undefined constant "%s"', $name));
        }

        return $this->constants[$name];
    }
}