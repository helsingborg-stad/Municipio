<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\MunicipioAuth\Visma;

/**
 * Interface for Visma authentication configuration.
 */
interface VismaAuthConfigInterface
{
    /**
     * Check if the configuration is valid
     *
     * @return bool
     */
    public function isValid(): bool;

    /**
     * Get the base URL for the Visma API
     *
     * @return string
     */
    public function getBaseUrl(): string;

    /**
     * Get the customer key for the Visma API
     *
     * @return string
     */
    public function getCustomerKey(): string;

    /**
     * Get the service key for the Visma API
     *
     * @return string
     */
    public function getServiceKey(): string;
}
