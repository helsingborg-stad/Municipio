<?php

namespace Municipio\PostsList\ViewUtilities\Table\TableArguments;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\AppearanceConfig\DefaultAppearanceConfig;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WP_Error;
use WP_Term;
use WpService\Contracts\GetTerms;

class TaxonomyTermsProviderTest extends TestCase
{
    #[TestDox('getAllTerms returns computed terms from WP service')]
    public function testGetAllTermsReturnsComputedTerms(): void
    {
        $appearanceConfig = new class extends DefaultAppearanceConfig {
            public function getTaxonomiesToDisplay(): array
            {
                return ['category', 'post_tag'];
            }
        };

        $wpService = new class implements GetTerms {
            public function getTerms(array|string $args = [], array|string $deprecated = ''): array|string|WP_Error
            {
                if ($args['taxonomy'] === ['category', 'post_tag']) {
                    return [
                        new WP_Term([]),
                        new WP_Term([]),
                    ];
                }

                return [];
            }
        };

        $termsProvider = new TaxonomyTermsProvider($appearanceConfig, [], $wpService);
        $terms         = $termsProvider->getAllTerms();

        $this->assertIsArray($terms);
        $this->assertCount(2, $terms);
    }

    #[TestDox('getAllTerms does not call WP service when no taxonomies are configured')]
    public function testGetAllTermsNoTaxonomiesConfigured(): void
    {
        $appearanceConfig = new class extends DefaultAppearanceConfig {
            public function getTaxonomiesToDisplay(): array
            {
                return [];
            }
        };
        $wpService        = new class implements GetTerms {
            public function getTerms(array|string $args = [], array|string $deprecated = ''): array|string|WP_Error
            {
                throw new \Exception('getTerms should not be called when no taxonomies are configured');
            }
        };

        $termsProvider = new TaxonomyTermsProvider($appearanceConfig, [], $wpService);
        $terms         = $termsProvider->getAllTerms();

        $this->assertIsArray($terms);
        $this->assertCount(0, $terms);
    }
}
