<?php

/**
 * NullApplicator.php
 *
 * Build tor mocking and testing purposes.
 *
 * @package Municipio\Customizer\Applicators\Types
 */

namespace Municipio\Customizer\Applicators\Types;

use Municipio\Customizer\Applicators\ApplicatorInterface;

class NullApplicator implements ApplicatorInterface
{
    // Return a fixed key for the NullApplicator
    public function getKey(): string
    {
        return 'null_applicator';
    }

    // Return a simple empty array for getData()
    public function getData(): array|string
    {
        return []; // No data to return
    }

    // Fake method to satisfy the interface, no actual data is applied
    public function applyData(array|object $data)
    {
        // No-op: Do nothing with the data for the null implementation
    }
}
