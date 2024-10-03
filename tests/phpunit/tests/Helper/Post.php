<?php

namespace Municipio\Tests\Helper;

use Mockery;
use WP_Mock;
use WP_Mock\Tools\TestCase;
use Municipio\Helper\Post;

/**
 * Class PostTest
 * @runTestsInSeparateProcesses
 * @group wp_mock
 */
class PostTest extends TestCase
{
    public function setUp(): void
    {
        $this->markTestSkipped('Post helper needs to handle missing deps. before tests can be relied upon.');
    }

    /**
     * @testdox preparePostObject returns a post if $post is an instance of WP_Post.
    */
    public function testPreparePostObjectReturnsPostIfPostReceived()
    {
        // Given
        $post = $this->mockPost(['ID' => 1, 'post_title' => 'Test', 'post_content' => 'Test']);
        $mock = $this->mockStaticMethod('\Municipio\Helper\Post', 'complementObject');
        $mock->once()->andReturn($post);

        // When
        $result = \Municipio\Helper\Post::preparePostObject($post);

        // Then
        $this->assertIsObject($result);
    }

    /**
     * @testdox preparePostObject: runtimeCache can add multiple posts with the same ID.
    */
    public function testPreparePostObjectCanAddMultiplePostsToRuntimeCacheUsingSameID()
    {
        // Given
        $post1 = $this->mockPost(['ID' => 1, 'post_date' => '2021-01-02 00:00:00']);
        $post2 = $this->mockPost(['ID' => 1, 'post_date' => '2021-01-01 00:00:00']);
        $mock  = $this->mockStaticMethod('\Municipio\Helper\Post', 'complementObject');
        $mock->andReturnUsing(fn ($post) => $post);

        // When
        \Municipio\Helper\Post::preparePostObject($post1);
        \Municipio\Helper\Post::preparePostObject($post2);

        // Then
        $this->assertCount(2, \Municipio\Helper\Post::$runtimeCache['preparePostObject']);
    }

     /**
     * @testdox preparePostObjectArchive returns a post if it $post is an instance of WP_Post.
     */
    public function testPreparePostObjectArchiveReturnsPostIfPostReceived()
    {
        // Given
        $post = $this->mockPost(['ID' => 1, 'post_title' => 'Test', 'post_content' => 'Test']);
        $mock = $this->mockStaticMethod('\Municipio\Helper\Post', 'complementObject');
        $mock->once()->andReturn($post);

        // When
        $result = \Municipio\Helper\Post::preparePostObjectArchive($post);

        // Then
        $this->assertIsObject($result);
    }

    /**
     * @testdox getPosttypeMetaKeys Always returns an array.
     * @backupGlobals enabled
     */
    public function testGetPosttypeMetaKeysReturnsArray()
    {
        // Given
        $this->getMockWpdb(['meta_key1', 'meta_key2']);

        // When
        $result = \Municipio\Helper\Post::getPosttypeMetaKeys('test');

        // Then
        $this->assertIsArray($result);
    }

    /**
     * @testdox getPosttypeMetaKeys Returns an array and filters certain values
     * @backupGlobals enabled
     */
    public function testGetPosttypeMetaKeysSkipCertainKeys()
    {
        // Given
        $this->getMockWpdb(['key1' => '_meta_key1', 'key2' => 'meta_key2']);

        // When
        $result = \Municipio\Helper\Post::getPosttypeMetaKeys('test');

        // Then
        $this->assertArrayNotHasKey('key1', $result);
        $this->assertArrayHasKey('key2', $result);
    }

    /**
     * @testdox getPostMetaKeys Always returns an array.
     * @backupGlobals enabled
     */
    public function testGetPostMetaKeysReturnsArray()
    {
        // Given
        $this->getMockWpdb(['meta_key1', 'meta_key2'], 'get_results');

        // When
        $result = \Municipio\Helper\Post::getPostMetaKeys('test');

        // Then
        $this->assertIsArray($result);
    }

    /**
     * @testdox getFeaturedImage returns an array if there is a thumbnailID.
     */
    public function testGetFeaturedImageReturnArrayIfFeaturedImageExists()
    {
        // Given
        WP_Mock::userFunction('get_post_thumbnail_id', [
        'return' => 1
        ]);

        Mockery::mock('alias:' . \Municipio\Helper\Image::class)->
        shouldReceive('getImageAttachmentData')->
        andReturn(['src' => 'test']);

        // When
        $result = \Municipio\Helper\Post::getFeaturedImage(1, 'full');

        $this->assertIsArray($result);
    }

    /**
     * @testdox getFeaturedImage returns false if there is no thumbnail ID
     */
    public function testGetFeaturedImageReturnFalseIfNoFeaturedImage()
    {
        // Given
        WP_Mock::userFunction('get_post_thumbnail_id', [
        'return' => ""
        ]);

        // When
        $result = \Municipio\Helper\Post::getFeaturedImage(null, 'full');

        // Then
        $this->assertFalse($result);
    }


    /**
     * @testdox ComplementObject Returns instance of WP_Post.
     */
    public function testComplementObjectReturnsPostObject()
    {
        // Given
        $post = $this->getMockedpost();
        $this->mockDependenciesForComplementObject($post);

        Mockery::mock('alias:' . \Municipio\Helper\Image::class)->
        shouldReceive('getImageAttachmentData')->
        andReturn(['src' => 'test']);

        // When
        $result = Post::complementObject($post);

        // Then
        $this->assertInstanceOf(\WP_Post::class, $result);
    }

    /**
     * @testdox ComplementObject Returns complemented post_excerpt keys.
     */
    public function testComplementObjectReturnsComplementedPostExcerptKeys()
    {
        // Given
        $post = $this->getMockedpost();
        $this->mockDependenciesForComplementObject($post);

        // When
        $result = Post::complementObject($post, [ 'excerpt']);

        // Then
        $this->assertIsString($result->excerpt);
        $this->assertIsString($result->excerpt_short);
        $this->assertIsString($result->excerpt_shorter);
    }

    /**
     * @testdox returns complemented default post_excerpt keys when empty post_excerpt and post_content.
     */
    public function testComplementObjectReturnsDefaultValuesIfEmptyPostExcerptAndPostContent()
    {
        // Given
        $post = $this->getMockedpost(['post_excerpt' => "", 'post_content' => ""]);
        $this->mockDependenciesForComplementObject($post, 'string', false, $post->post_content, $post->post_content);

        // When
        $result = Post::complementObject($post, [ 'excerpt']);

        // Then
        $this->assertEquals($result->excerpt, '<span class="undefined-content">Item is missing content</span>');
        $this->assertEquals($result->excerpt_short, '');
        $this->assertEquals($result->excerpt_shorter, '');
    }

    /**
     * @testdox Returns complemented default post_excerpt keys when empty post_excerpt and post_content.
     */
    public function testComplementObjectReturnsDefaultValuesIfEmptyPostExcerpt()
    {
        // Given
        $post = $this->getMockedpost(['post_excerpt' => "", 'post_content' => "test"]);
        $this->mockDependenciesForComplementObject($post, 'string', false, $post->post_content, $post->post_content);

        // When
        $result = Post::complementObject($post, [ 'excerpt']);

        // Then
        $this->assertEquals($result->excerpt, $post->post_content);
        $this->assertEquals($result->excerpt_short, $post->post_content);
        $this->assertEquals($result->excerpt_shorter, $post->post_content);
    }

    /**
     * @testdox ComplementObject Returns complemented a post_excerpt if more tag
     */
    public function testComplementObjectReturnsExcerptFromPostContentIfMoreTag()
    {
        // Given
        $post = $this->getMockedpost([
            'post_excerpt' => "",
            'post_content' => 'Some text. <!--more--> Some other text'
        ]);

        $this->mockDependenciesForComplementObject($post, 'string', false, $post->post_content, $post->post_content);

        // When
        $result = Post::complementObject($post, [ 'excerpt']);

        // Then
        $this->assertEquals('Some text. ', $result->excerpt);
    }

    /**
     * @testdox ComplementObject Returns complemented post_title keys.
     */
    public function testComplementObjectReturnsComplementedPostTitleKeys()
    {
        // Given
        $post = $this->getMockedpost();
        $this->mockDependenciesForComplementObject($post);

        // When
        $result = Post::complementObject($post, [ 'post_title_filtered']);

        // Then
        $this->assertIsString($result->post_title_filtered);
    }

    /**
     * @testdox ComplementObject Returns complemented permalink keys.
     */
    public function testComplementObjectReturnsComplementedPostPermalinkKeys()
    {
        // Given
        $post = $this->getMockedpost();
        $this->mockDependenciesForComplementObject($post);

        // When
        $result = Post::complementObject($post, [ 'permalink']);

        // Then
        $this->assertIsString($result->permalink);
    }

    /**
     * @testdox ComplementObject Returns complemented terms keys.
     */
    public function testComplementObjectReturnsComplementedPostTermsKeys()
    {
        // Given
        $post = $this->getMockedpost();
        $this->mockDependenciesForComplementObject($post);

        // When
        $result = Post::complementObject($post, ['terms']);

        // Then
        $this->assertIsArray($result->terms);
        $this->assertIsArray($result->termsUnlinked);
    }

    /**
     * @testdox ComplementObject Returns complemented langauge keys.
     */
    public function testComplementObjectReturnsComplementedPostlanguageKeys()
    {
        // Given
        $post = $this->getMockedpost();
        $this->mockDependenciesForComplementObject($post);

        // When
        $result = Post::complementObject($post, ['post_language']);

        // Then
        $this->assertIsString($result->post_language);
    }

    /**
     * @testdox ComplementObject Returns complemented reading time keys.
     */
    public function testComplementObjectReturnsComplementedPostReadingTimeKeys()
    {
        // Given
        $post = $this->getMockedpost();
        $this->mockDependenciesForComplementObject($post);

        // When
        $result = Post::complementObject($post, ['reading_time']);

        // Then
        $this->assertIsString($result->reading_time);
    }

    // /**
    //  * @testdox ComplementObject Returns complemented call to action items keys.
    //  */
    public function testComplementObjectReturnsComplementedPostCallToActionItemsKeys()
    {
        // Given
        $post = $this->getMockedpost();
        $this->mockDependenciesForComplementObject($post);

        // When
        $result = Post::complementObject($post, ['call_to_action_items']);

        // Then
        $this->assertIsArray($result->call_to_action_items);
    }

    // /**
    //  * @testdox ComplementObject Returns complemented term icon keys.
    //  */
    public function testComplementObjectReturnsComplementedPostTermIconKeys()
    {
        // Given
        $post = $this->getMockedpost();
        $this->mockDependenciesForComplementObject($post);

        // When
        $result = Post::complementObject($post, ['term_icon']);


        // Then
        $this->assertIsString($result->termIcon['icon']);
        $this->assertIsString($result->termIcon['backgroundColor']);
        $this->assertIsString($result->termIcon['color']);
        $this->assertIsString($result->termIcon['size']);
    }

    /**
     * @testdox ComplementObject Returns correct keys that are always set
     */
    public function testComplementObjectReturnsComplementedAlwaysSetKeys()
    {
        // Given
        $post = $this->getMockedpost();
        $this->mockDependenciesForComplementObject($post);

        // When
        $result = Post::complementObject($post, []);


        // Then
        $this->assertArrayHasKey('thumbnail_16:9', $result->images);
        $this->assertArrayHasKey('thumbnail_4:3', $result->images);
        $this->assertArrayHasKey('thumbnail_1:1', $result->images);
        $this->assertArrayHasKey('thumbnail_3:4', $result->images);
        $this->assertArrayHasKey('featuredImage', $result->images);
        $this->assertArrayHasKey('thumbnail_12:16', $result->images);
        $this->assertIsString($result->post_time_formatted);
        $this->assertIsString($result->post_date_time_formatted);
        $this->assertIsString($result->post_date_formatted);
    }

    /**
     * @testdox ComplementObject Changes content keys if password protected.
     */
    public function testComplementObjectReturnsSkipKeysWhenPasswordProtected()
    {
        // Given
        $post = $this->getMockedpost();
        $this->mockDependenciesForComplementObject($post, 'string', true);

        // When
        $result = Post::complementObject($post, []);

        // Then
        $this->assertEquals($post->post_content, $result->post_content);
        $this->assertEquals($post->post_content, $result->post_content_filtered);
        $this->assertEquals($post->post_content, $result->post_excerpt);
        $this->assertEquals($post->post_content, $result->excerpt);
        $this->assertEquals($post->post_content, $result->excerpt_short);
        $this->assertEquals($post->post_content, $result->excerpt_shorter);
    }

    /**
     * @testdox ComplementObject Places quicklinks after first block if chosen.
     */
    public function testComplementObjectReturnsCorrectQuicklinksPlacement()
    {
        // Given
        $post = $this->getMockedpost();

        WP_Mock::userFunction('parse_blocks', [
            'return' => [(object)['blockName' => 'block1'], (object)['blockName' => 'block2']]
        ]);

        WP_Mock::userFunction('render_block', [
            'return' => '<div>block</div>'
        ]);

        WP_Mock::userFunction('render_blade_view', [
            'return' => '<div>quicklinks</div>'
        ]);

        $this->mockDependenciesForComplementObject($post);

        // When
        $result = Post::complementObject($post, [], [
            'lang'                => 'test',
            'customizer'          => 'test',
            'quicklinksMenuItems' => ['item 1', 'item 2']
        ]);

        // Then
        $this->assertEquals('<div>block</div><div>quicklinks</div><div>block</div>', $result->post_content);
    }

    /**
     * @testdox ComplementObject Returns formatted post_content as post_content_filtered when a more-tag is found
     */
    public function testComplementObjectReturnsFilteredPostContent()
    {
        // Given
        $post = $this->getMockedpost(['post_content' => 'Some text . <!--more--> Some other text']);
        $this->mockDependenciesForComplementObject($post);

        // When
        $result = Post::complementObject($post, ['post_content_filtered']);

        // Then
        $this->assertEquals($result->post_content_filtered, '<p class="lead">Some text . </p> Some other text');
    }

    // Mock post args
    private function getMockedpost(array $args = [])
    {
        return $this->mockPost(array_merge([
            'ID'           => 1,
            'post_title'   => 'test',
            'post_content' => 'test',
            'post_excerpt' => 'Test',
            'permalink'    => 'https://url.url',
            'post_type'    => 'test',
            'terms'        => ['test' => 'test']
        ], $args));
    }

    // Mock term object
    private function mockTermObject()
    {
        $termMock           = Mockery::mock('WP_Term');
        $termMock->term_id  = 1;
        $termMock->taxonomy = 'test-taxonomy';
        $termMock->slug     = 'test-slug';

        return $termMock;
    }

    // Mock ComplementObject methods
    private function mockDependenciesForComplementObject(
        $post,
        $getField = 'string',
        $postPasswordRequired = false,
        $wpTrimWords = null,
        $stripShortcodes = null
    ) {
        WP_Mock::userFunction('post_password_required', [
            'return' => $postPasswordRequired
        ]);

        WP_Mock::userFunction('get_the_password_form', [
            'return' => $post->post_content
        ]);

        WP_Mock::userFunction('wp_date', [
            'return' => 'TestDate'
        ]);

        WP_Mock::userFunction('get_post_thumbnail_id', [
            'return' => 1
        ]);

        WP_Mock::userFunction('get_field', [
            'return' => $getField
        ]);

        WP_Mock::userFunction('strip_shortcodes', [
            'return' => $stripShortcodes ?? $post->post_excerpt
        ]);

        WP_Mock::userFunction('wp_trim_words', [
            'return' => $wpTrimWords ?? $post->post_excerpt
        ]);

        WP_Mock::userFunction('get_permalink', [
            'return' => $post->permalink
        ]);

        WP_Mock::userFunction('get_theme_mod', [
            'return' => ['modName' => 'modValue']
        ]);

        WP_Mock::userFunction('get_post_type', [
            'return' => $post->post_type
        ]);

        WP_Mock::userFunction('get_bloginfo', [
            'return' => 'en'
        ]);

        WP_Mock::userFunction('wp_get_post_terms', [
            'return' => [$this->mockTermObject()]
        ]);

        WP_Mock::userFunction('get_term_link', [
            'return' => 'https://test.url'
        ]);

        WP_Mock::userFunction('get_object_taxonomies', [
            'return' => (object) ['icon' => 'test']
        ]);

        WP_Mock::userFunction('get_option', [
            'return' => 'test'
        ]);

        WP_Mock::userFunction('get_the_terms', [
            'return' => (object) ['icon' => 'test']
        ]);

        WP_Mock::userFunction('get_term_by', [
            'return' => ['term' => 'test']
        ]);

        WP_Mock::userFunction('has_blocks', [
            'return' => true
        ]);

        Mockery::mock('alias:' . \Municipio\Helper\Image::class)->
        shouldReceive('getImageAttachmentData')->
        andReturn(['src' => 'test']);

        Mockery::mock('alias:' . \Municipio\Helper\Term::class)->
        shouldReceive('getTermIcon')->
        andReturn(['src' => 'test', 'type' => 'icon'])->
        shouldReceive('getTermColor')->
        andReturn('blue');

        Mockery::mock('alias:' . \Municipio\Helper\Navigation::class)->
        shouldReceive('getQuicklinksPlacement')->
        andReturn('after_first_block')->
        shouldReceive('displayQuicklinksAfterContent')->
        andReturn(false);

        Mockery::mock('alias:' . \Modularity\Module\Posts\Helper\ContentType::class)->
        shouldReceive('getContentType')->
        andReturn(false);
    }

    /**
     * Wpdb mock
    */
    private function getMockWpdb($returnValue = false, $shouldReceive = 'get_col')
    {
        $wpdbMock = Mockery::mock('WPDB');
        $wpdbMock->shouldReceive($shouldReceive)
        ->andReturn($returnValue);
        $GLOBALS['wpdb']           = $wpdbMock;
        $GLOBALS['wpdb']->postmeta = 'test';
        $GLOBALS['wpdb']->posts    = 'test';
    }
}
