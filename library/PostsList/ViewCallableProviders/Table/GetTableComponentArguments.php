<?php

namespace Municipio\PostsList\ViewCallableProviders\Table;

use Municipio\PostObject\PostObjectInterface;
use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\ViewCallableProviders\Table\TableArguments\TableHeadingsGenerator;
use Municipio\PostsList\ViewCallableProviders\Table\TableArguments\TableItemsGenerator;
use Municipio\PostsList\ViewCallableProviders\Table\TableArguments\TableItemsWithEmpasizedFirstItem;
use Municipio\PostsList\ViewCallableProviders\ViewCallableProviderInterface;
use WpService\WpService;

/**
 * Get arguments for the table component
 */
class GetTableComponentArguments implements ViewCallableProviderInterface
{
    public const FILTER_HOOK = 'Municipio/PostsList/Table/Arguments';

    /**
     * Constructor
     *
     * @param PostObjectInterface[] $posts
     * @param AppearanceConfigInterface $appearanceConfig
     * @param string[] $postTypes
     */
    public function __construct(
        private array $posts,
        private AppearanceConfigInterface $appearanceConfig,
        private WpService $wpService,
        private array $postTypes = [],
    ) {}

    /**
     * Get a callable that returns table component arguments
     *
     * @return callable
     */
    public function getCallable(): callable
    {
        return fn() => $this->getTableArguments();
    }

    /**
     * Get table arguments including headings and items
     *
     * @return array
     */
    private function getTableArguments(): array
    {
        $headingsGenerator = new TableHeadingsGenerator($this->appearanceConfig, $this->posts, $this->wpService);
        $itemsGenerator = new TableItemsGenerator(
            $this->appearanceConfig,
            $this->posts,
            $this->wpService,
            new TableArguments\TaxonomyTermsProvider($this->appearanceConfig, $this->posts, $this->wpService),
            new TableArguments\LabelFormatter($this->wpService),
        );

        $tableArguments = $this->wpService->applyFilters(
            self::FILTER_HOOK,
            [
                'headings' => $headingsGenerator->generate(),
                'list'     => $itemsGenerator->generate(),
            ],
            $this->posts,
            $this->postTypes,
        );

        $tableArguments['list'] = (new TableItemsWithEmpasizedFirstItem($tableArguments['list']))->emphasize();

        return $tableArguments;
    }
}
