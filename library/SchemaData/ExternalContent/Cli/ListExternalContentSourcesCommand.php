<?php

declare(strict_types=1);

namespace Municipio\SchemaData\ExternalContent\Cli;

use Municipio\SchemaData\ExternalContent\Config\SourceConfigInterface;

/**
 * Lists post types that have a configured external content source.
 */
class ListExternalContentSourcesCommand
{
    /**
     * @param SourceConfigInterface[] $sourceConfigs
     */
    public function __construct(private array $sourceConfigs)
    {
    }

    /**
     * Register the WP-CLI list command.
     */
    public function register(): void
    {
        $this->callWpCli('add_command', 'municipio external-content list', [$this, 'list']);
    }

    /**
     * List post types with a configured external content source.
     *
     * ## EXAMPLES
     *
     *     wp municipio external-content list
     */
    public function list(array $arguments, array $associativeArguments): void
    {
        if ($this->sourceConfigs === []) {
            $this->callWpCli('log', 'No external content sources are configured.');
            return;
        }

        foreach ($this->sourceConfigs as $config) {
            $this->callWpCli('log', sprintf(
                '%s (schema: %s, source: %s, schedule: %s)',
                $config->getPostType(),
                $config->getSchemaType(),
                $config->getSourceType(),
                $config->getAutomaticImportSchedule(),
            ));
        }
    }

    /**
     * Invoke the WP-CLI runtime without requiring its classes in web requests.
     */
    private function callWpCli(string $method, mixed ...$arguments): mixed
    {
        return call_user_func_array(['WP_CLI', $method], $arguments);
    }
}
