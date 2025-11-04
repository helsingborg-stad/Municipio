<?php

namespace Municipio\PostsList\ViewUtilities\Table;

use Municipio\PostObject\PostObjectInterface;
use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\ViewUtilities\ViewUtilityInterface;
use Municipio\PostsList\ViewUtilities\Table\TableArguments\{TableHeadingsGenerator, TableItemsGenerator};
use WpService\WpService;

/**
 * Get arguments for the table component
 */
class GetTableComponentArguments implements ViewUtilityInterface
{
    /**
     * Constructor
     *
     * @param PostObjectInterface[] $posts
     * @param AppearanceConfigInterface $appearanceConfig
     */
    public function __construct(
        private array $posts,
        private AppearanceConfigInterface $appearanceConfig,
        private WpService $wpService
    ) {
    }

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
        $itemsGenerator    = new TableItemsGenerator(
            $this->appearanceConfig,
            $this->posts,
            $this->wpService,
            new TableArguments\TaxonomyTermsProvider($this->appearanceConfig, $this->posts, $this->wpService),
            new TableArguments\LabelFormatter($this->wpService)
        );

        return [
            'headings' => $headingsGenerator->generate(),
            'list'     => $itemsGenerator->generate(),
        ];
    }
}
