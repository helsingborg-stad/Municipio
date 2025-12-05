<?php

namespace Municipio\PostsList\ViewCallableProviders;

use Municipio\PostObject\NullPostObject;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WP_Error;
use WP_Term;

class TestPostObjectWithId extends NullPostObject
{
    public function __construct(private int $id)
    {
    }

    public function getId(): int
    {
        return $this->id;
    }
}

class TestAcfService implements \AcfService\Contracts\GetField
{
    public function getField(string $selector, int|false|string $postId = false, bool $formatValue = true, bool $escapeHtml = false)
    {
        return 'color';
    }
}

class TestWpService implements \WpService\Contracts\GetTerms
{
    public $getTermsCallCount = 0;
    public function getTerms(array|string $args = [], array|string $deprecated = ''): array|string|WP_Error
    {
        $this->getTermsCallCount++;
        $term1            = new WP_Term([]);
        $term1->term_id   = 1;
        $term1->name      = 'Tag One';
        $term1->slug      = 'tag-one';
        $term1->taxonomy  = 'post_tag';
        $term1->object_id = 1;

        $term2            = new WP_Term([]);
        $term2->term_id   = 2;
        $term2->name      = 'Tag Two';
        $term2->slug      = 'tag-two';
        $term2->taxonomy  = 'post_tag';
        $term2->object_id = 2;

        return [ $term1, $term2 ];
    }
}

class GetTagsComponentArgumentsTest extends TestCase
{
    #[TestDox('returns tags with correct structure')]
    public function testReturnsTagsWithCorrectStructure(): void
    {
        $posts                     = [ new TestPostObjectWithId(1) ];
        $getTagsComponentArguments = new GetTagsComponentArguments($posts, ['post_tag'], new TestWpService(), new TestAcfService());

        $this->assertEquals([[
        'label'    => 'Tag One',
        'slug'     => 'tag-one',
        'taxonomy' => 'post_tag',
        'color'    => 'color'
        ]], $getTagsComponentArguments->getCallable()($posts[0]));
    }

    #[TestDox('only calls getTerms once for multiple posts (to ensure memoization works)')]
    public function testOnlyCallsGetTermsOnceForMultiplePosts(): void
    {
        $posts                     = [ new TestPostObjectWithId(1), new TestPostObjectWithId(2) ];
        $wpService                 = new TestWpService();
        $getTagsComponentArguments = new GetTagsComponentArguments($posts, ['post_tag'], $wpService, new TestAcfService());

        // Call for both posts
        $getTagsComponentArguments->getCallable()($posts[0]);
        $getTagsComponentArguments->getCallable()($posts[1]);

        // Assert getTerms was only called once
        $this->assertEquals(1, $wpService->getTermsCallCount);
    }

    #[TestDox('returns empty array when no tags are found for a post')]
    public function testReturnsEmptyArrayWhenNoTagsFound(): void
    {
        $posts                     = [ new TestPostObjectWithId(3) ];
        $wpService                 = new TestWpService();
        $getTagsComponentArguments = new GetTagsComponentArguments($posts, ['post_tag'], $wpService, new TestAcfService());

        $this->assertEquals([], $getTagsComponentArguments->getCallable()($posts[0]));
    }

    #[TestDox('does not call getTerms when taxonomies array is empty')]
    public function testDoesNotCallGetTermsWhenTaxonomiesEmpty(): void
    {
        $posts                     = [ new TestPostObjectWithId(1) ];
        $wpService                 = new TestWpService();
        $getTagsComponentArguments = new GetTagsComponentArguments($posts, [], $wpService, new TestAcfService());

        $this->assertEquals([], $getTagsComponentArguments->getCallable()($posts[0]));
        $this->assertEquals(0, $wpService->getTermsCallCount);
    }
}
