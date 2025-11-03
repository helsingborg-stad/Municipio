<?php

namespace Municipio\PostsList\ViewUtilities;

use Municipio\PostObject\NullPostObject;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WP_Error;
use WP_Term;
use WpService\Contracts\GetTerms;

class GetTermsAsStringTest extends TestCase
{
    #[TestDox('It returns a comma-separated string of term names for a given post')]
    public function testGetTermsAsString(): void
    {
        $post  = new class extends NullPostObject {
            public function getId(): int
            {
                return 1;
            }
        };
        $posts = [$post];

        $wpService = new class implements GetTerms {
            public function getTerms(array|string $args = [], array|string $deprecated = ''): array|string|WP_Error
            {
                $termOne            = new WP_Term([]);
                $termOne->name      = 'Term One';
                $termOne->object_id = 1;

                $termTwo            = new WP_Term([]);
                $termTwo->name      = 'Term Two';
                $termTwo->object_id = 1;

                return [$termOne, $termTwo];
            }
        };

        $getTermsAsString = new GetTermsAsString($posts, ['category'], $wpService, ' / ');

        $callable = $getTermsAsString->getCallable();
        $result   = $callable($posts[0]);

        $this->assertEquals('Term One / Term Two', $result);
    }

    #[TestDox('It returns an empty string when no taxonomies are provided')]
    public function testGetTermsAsStringNoTaxonomies(): void
    {
        $post  = new class extends NullPostObject {
            public function getId(): int
            {
                return 1;
            }
        };
        $posts = [$post];

        $wpService = new class implements GetTerms {
            public function getTerms(array|string $args = [], array|string $deprecated = ''): array|string|WP_Error
            {
                return [];
            }
        };

        $getTermsAsString = new GetTermsAsString($posts, [], $wpService);

        $callable = $getTermsAsString->getCallable();
        $result   = $callable($posts[0]);

        $this->assertEquals('', $result);
    }
}
