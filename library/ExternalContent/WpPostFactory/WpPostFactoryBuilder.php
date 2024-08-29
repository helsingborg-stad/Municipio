<?php

namespace Municipio\ExternalContent\WpPostFactory;

use Municipio\ExternalContent\WpTermFactory\WpTermFactoryInterface;
use WpService\WpService;

class WpPostFactoryBuilder implements WpPostFactoryBuilderInterface
{
    /**
     * Class constructor
     *
     * @param TaxonomyItemInterface[] $taxonomyItems
     * @param WpTermFactoryInterface $wpTermFactory
     * @param WpService $wpService
     */
    public function __construct(
        private array $taxonomyItems,
        private WpTermFactoryInterface $wpTermFactory,
        private WpService $wpService
    ) {
    }

    public function build(): WpPostFactoryInterface
    {
        $wpPostFactory = new \Municipio\ExternalContent\WpPostFactory\WpPostFactory();
        $wpPostFactory = new \Municipio\ExternalContent\WpPostFactory\DateDecorator($wpPostFactory);
        $wpPostFactory = new \Municipio\ExternalContent\WpPostFactory\IdDecorator($wpPostFactory, $this->wpService);
        $wpPostFactory = new \Municipio\ExternalContent\WpPostFactory\JobPostingDecorator($wpPostFactory);
        $wpPostFactory = new \Municipio\ExternalContent\WpPostFactory\SchemaDataDecorator($wpPostFactory);
        $wpPostFactory = new \Municipio\ExternalContent\WpPostFactory\OriginIdDecorator($wpPostFactory);
        $wpPostFactory = new \Municipio\ExternalContent\WpPostFactory\ThumbnailDecorator($wpPostFactory, $this->wpService);
        $wpPostFactory = new \Municipio\ExternalContent\WpPostFactory\SourceIdDecorator($wpPostFactory);
        $wpPostFactory = new \Municipio\ExternalContent\WpPostFactory\VersionDecorator($wpPostFactory);
        $wpPostFactory = new \Municipio\ExternalContent\WpPostFactory\TermsDecorator($this->taxonomyItems, $this->wpTermFactory, $this->wpService, $wpPostFactory);

        return $wpPostFactory;
    }
}
