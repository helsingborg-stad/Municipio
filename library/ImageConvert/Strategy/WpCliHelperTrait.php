<?php

namespace Municipio\ImageConvert\Strategy;

/**
 * WP CLI Helper Trait
 * 
 * Provides common WP CLI functionality for strategies that need to interact with WP CLI.
 * Centralizes WP CLI detection and output methods to avoid code duplication.
 */
trait WpCliHelperTrait
{
    /**
     * Check if we're running in WP CLI context
     * 
     * @return bool
     */
    protected function isWpCli(): bool
    {
        return defined('WP_CLI') && WP_CLI;
    }

    /**
     * Get WP CLI instance if available
     * 
     * @return \WP_CLI|null
     */
    protected function getWpCli(): ?\WP_CLI
    {
        return $this->isWpCli() ? \WP_CLI::class : null;
    }

    /**
     * Output a warning message via WP CLI if available
     * 
     * @param string $message
     * @return void
     */
    protected function wpCliWarning(string $message): void
    {
        if ($this->isWpCli()) {
            \WP_CLI::warning($message);
        }
    }

    /**
     * Output a success message via WP CLI if available
     * 
     * @param string $message
     * @return void
     */
    protected function wpCliSuccess(string $message): void
    {
        if ($this->isWpCli()) {
            \WP_CLI::success($message);
        }
    }

    /**
     * Output an error message via WP CLI if available
     * 
     * @param string $message
     * @return void
     */
    protected function wpCliError(string $message): void
    {
        if ($this->isWpCli()) {
            \WP_CLI::error($message);
        }
    }

    /**
     * Output a line message via WP CLI if available
     * 
     * @param string $message
     * @return void
     */
    protected function wpCliLine(string $message): void
    {
        if ($this->isWpCli()) {
            \WP_CLI::line($message);
        }
    }
}