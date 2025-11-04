<?php

namespace Municipio\PostsList\ViewUtilities\Table\TableArguments;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use WpService\Contracts\GetTerms;

class TaxonomyTermsProvider implements TaxonomyTermsProviderInterface
{
    private array $terms;

    public function __construct(
        private AppearanceConfigInterface $appearanceConfig,
        private array $posts,
        private GetTerms $wpService
    ) {
        $this->terms = $this->computeAllTerms();
    }

    public function getAllTerms(): array
    {
        return $this->terms;
    }

    private function computeAllTerms(): array
    {
        if (empty($this->appearanceConfig->getTaxonomiesToDisplay())) {
            return [];
        }
        return $this->wpService->getTerms([
            'taxonomy'   => $this->appearanceConfig->getTaxonomiesToDisplay(),
            'hide_empty' => false,
            'object_ids' => array_map(fn($post) => $post->getId(), $this->posts),
            'fields'     => 'all_with_object_id',
        ]);
    }
}
