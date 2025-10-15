<?php

namespace Modularity\Module\Posts\Helper\GetPosts;

use Modularity\Helper\WpQueryFactory\WpQueryFactoryInterface;
use Modularity\Module\Posts\Helper\GetPosts\PostTypesFromSchemaType\NullPostTypesFromSchemaTypeResolver;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use WP_Post;
use WpService\Implementations\FakeWpService;

class GetPostsTest extends TestCase {

    #[TestDox('getCurrentPostID() returns the current post ID')]
    public function testGetCurrentPostIDReturnsTheCurrentPostID() {
        $wpService = new FakeWpService(['getTheID' => 1, 'isArchive' => false]);
        $wpQueryFactory = $this->createStub(WpQueryFactoryInterface::class);
        $postTypeFromSchemaTypeResolver = new NullPostTypesFromSchemaTypeResolver();
        
        $getPosts = new GetPosts([], 1, null, $wpService, $wpQueryFactory, $postTypeFromSchemaTypeResolver);

        $this->assertEquals(1, $getPosts->getCurrentPostID());
    }
    
    #[TestDox('getCurrentPostID() returns false when post is not set')]
    public function testGetCurrentPostIDReturnsFalseWhenPostIsNotSet() {
        $wpService = new FakeWpService(['getTheID' => false, 'isArchive' => false]);
        $wpQueryFactory = $this->createStub(WpQueryFactoryInterface::class);
        $postTypeFromSchemaTypeResolver = new NullPostTypesFromSchemaTypeResolver();

        $getPosts = new GetPosts([], 1, null, $wpService, $wpQueryFactory, $postTypeFromSchemaTypeResolver);

        $this->assertFalse($getPosts->getCurrentPostID());
    }
    
    #[TestDox('getCurrentPostID() returns false when in archive context')]
    public function testGetCurrentPostIDReturnsFalseWhenInArchiveContext() {
        $wpService = new FakeWpService(['getTheID' => 1, 'isArchive' => true]);
        $wpQueryFactory = $this->createStub(WpQueryFactoryInterface::class);
        $postTypeFromSchemaTypeResolver = new NullPostTypesFromSchemaTypeResolver();

        $getPosts = new GetPosts([], 1, null, $wpService, $wpQueryFactory, $postTypeFromSchemaTypeResolver);

        $this->assertFalse($getPosts->getCurrentPostID());
    }

    #[TestDox('sortPosts() sorts posts by date descending')]
    public function testSortPostsSortsPostsByDate() {
        $posts = [
            $this->getWpPostMock(['post_date' => '2021-01-01']),
            $this->getWpPostMock(['post_date' => '2021-01-03']),
            $this->getWpPostMock(['post_date' => '2021-01-02']),
        ];

        $wpService = new FakeWpService(['getTheID' => 1, 'isArchive' => false]);
        $wpQueryFactory = $this->createStub(WpQueryFactoryInterface::class);
        $postTypeFromSchemaTypeResolver = new NullPostTypesFromSchemaTypeResolver();

        $getPosts = new GetPosts([], 1, null, $wpService, $wpQueryFactory, $postTypeFromSchemaTypeResolver);

        $sortedPosts = $getPosts->sortPosts($posts, 'date', 'desc');

        $this->assertEquals('2021-01-03', $sortedPosts[0]->post_date);
        $this->assertEquals('2021-01-02', $sortedPosts[1]->post_date);
        $this->assertEquals('2021-01-01', $sortedPosts[2]->post_date);
    }

    #[TestDox('sortPosts() sorts posts by date ascending')]
    public function testSortPostsSortsPostsByDateAscending() {
        $posts = [
            $this->getWpPostMock(['post_date' => '2021-01-01']),
            $this->getWpPostMock(['post_date' => '2021-01-03']),
            $this->getWpPostMock(['post_date' => '2021-01-02']),
        ];

        $wpService = new FakeWpService(['getTheID' => 1, 'isArchive' => false]);
        $wpQueryFactory = $this->createStub(WpQueryFactoryInterface::class);
        $postTypeFromSchemaTypeResolver = new NullPostTypesFromSchemaTypeResolver();

        $getPosts = new GetPosts([], 1, null, $wpService, $wpQueryFactory, $postTypeFromSchemaTypeResolver);

        $sortedPosts = $getPosts->sortPosts($posts, 'date', 'asc');

        $this->assertEquals('2021-01-01', $sortedPosts[0]->post_date);
        $this->assertEquals('2021-01-02', $sortedPosts[1]->post_date);
        $this->assertEquals('2021-01-03', $sortedPosts[2]->post_date);
    }

    #[TestDox('sortPosts() sorts posts by title descending')]
    public function testSortPostsSortsPostsByTitleDescending() {
        $posts = [
            $this->getWpPostMock(['post_title' => 'C']),
            $this->getWpPostMock(['post_title' => 'A']),
            $this->getWpPostMock(['post_title' => 'B']),
        ];

        $wpService = new FakeWpService(['getTheID' => 1, 'isArchive' => false]);
        $wpQueryFactory = $this->createStub(WpQueryFactoryInterface::class);
        $postTypeFromSchemaTypeResolver = new NullPostTypesFromSchemaTypeResolver();

        $getPosts = new GetPosts([], 1, null, $wpService, $wpQueryFactory, $postTypeFromSchemaTypeResolver);

        $sortedPosts = $getPosts->sortPosts($posts, 'title', 'desc');

        $this->assertEquals('C', $sortedPosts[0]->post_title);
        $this->assertEquals('B', $sortedPosts[1]->post_title);
        $this->assertEquals('A', $sortedPosts[2]->post_title);
    }

    #[TestDox('sortPosts() sorts posts by title ascending')]
    public function testSortPostsSortsPostsByTitleAscending() {
        $posts = [
            $this->getWpPostMock(['post_title' => 'C']),
            $this->getWpPostMock(['post_title' => 'A']),
            $this->getWpPostMock(['post_title' => 'B']),
        ];

        $wpService = new FakeWpService(['getTheID' => 1, 'isArchive' => false]);
        $wpQueryFactory = $this->createStub(WpQueryFactoryInterface::class);
        $postTypeFromSchemaTypeResolver = new NullPostTypesFromSchemaTypeResolver();

        $getPosts = new GetPosts([], 1, null, $wpService, $wpQueryFactory, $postTypeFromSchemaTypeResolver);

        $sortedPosts = $getPosts->sortPosts($posts, 'title', 'asc');

        $this->assertEquals('A', $sortedPosts[0]->post_title);
        $this->assertEquals('B', $sortedPosts[1]->post_title);
        $this->assertEquals('C', $sortedPosts[2]->post_title);
    }

    #[TestDox('sortPosts() sorts posts by modified date descending')]
    public function testSortPostsSortsPostsByModifiedDateDescending() {
        $posts = [
            $this->getWpPostMock(['post_modified' => '2021-01-01']),
            $this->getWpPostMock(['post_modified' => '2021-01-03']),
            $this->getWpPostMock(['post_modified' => '2021-01-02']),
        ];

        $wpService = new FakeWpService(['getTheID' => 1, 'isArchive' => false]);
        $wpQueryFactory = $this->createStub(WpQueryFactoryInterface::class);
        $postTypeFromSchemaTypeResolver = new NullPostTypesFromSchemaTypeResolver();

        $getPosts = new GetPosts([], 1, null, $wpService, $wpQueryFactory, $postTypeFromSchemaTypeResolver);

        $sortedPosts = $getPosts->sortPosts($posts, 'modified', 'desc');

        $this->assertEquals('2021-01-03', $sortedPosts[0]->post_modified);
        $this->assertEquals('2021-01-02', $sortedPosts[1]->post_modified);
        $this->assertEquals('2021-01-01', $sortedPosts[2]->post_modified);
    }

    #[TestDox('sortPosts() sorts posts by modified date ascending')]
    public function testSortPostsSortsPostsByModifiedDateAscending() {
        $posts = [
            $this->getWpPostMock(['post_modified' => '2021-01-01']),
            $this->getWpPostMock(['post_modified' => '2021-01-03']),
            $this->getWpPostMock(['post_modified' => '2021-01-02']),
        ];

        $wpService = new FakeWpService(['getTheID' => 1, 'isArchive' => false]);
        $wpQueryFactory = $this->createStub(WpQueryFactoryInterface::class);
        $postTypeFromSchemaTypeResolver = new NullPostTypesFromSchemaTypeResolver();

        $getPosts = new GetPosts([], 1, null, $wpService, $wpQueryFactory, $postTypeFromSchemaTypeResolver);

        $sortedPosts = $getPosts->sortPosts($posts, 'modified', 'asc');

        $this->assertEquals('2021-01-01', $sortedPosts[0]->post_modified);
        $this->assertEquals('2021-01-02', $sortedPosts[1]->post_modified);
        $this->assertEquals('2021-01-03', $sortedPosts[2]->post_modified);
    }

    #[TestDox('sortPosts() sorts posts by menu order descending')]
    public function testGetPostsSortsPostsByMenuOrderDescending() {
        $posts = [
            $this->getWpPostMock(['menu_order' => 3]),
            $this->getWpPostMock(['menu_order' => 1]),
            $this->getWpPostMock(['menu_order' => 2]),
        ];

        $wpService = new FakeWpService(['getTheID' => 1, 'isArchive' => false]);
        $wpQueryFactory = $this->createStub(WpQueryFactoryInterface::class);
        $postTypeFromSchemaTypeResolver = new NullPostTypesFromSchemaTypeResolver();

        $getPosts = new GetPosts([], 1, null, $wpService, $wpQueryFactory, $postTypeFromSchemaTypeResolver);

        $sortedPosts = $getPosts->sortPosts($posts, 'menu_order', 'desc');

        $this->assertEquals(3, $sortedPosts[0]->menu_order);
        $this->assertEquals(2, $sortedPosts[1]->menu_order);
        $this->assertEquals(1, $sortedPosts[2]->menu_order);
    }

    #[TestDox('sortPosts() sorts posts by menu order ascending')]
    public function testGetPostsSortsPostsByMenuOrderAscending() {
        $posts = [
            $this->getWpPostMock(['menu_order' => 3]),
            $this->getWpPostMock(['menu_order' => 1]),
            $this->getWpPostMock(['menu_order' => 2]),
        ];

        $wpService = new FakeWpService(['getTheID' => 1, 'isArchive' => false]);
        $wpQueryFactory = $this->createStub(WpQueryFactoryInterface::class);
        $postTypeFromSchemaTypeResolver = new NullPostTypesFromSchemaTypeResolver();

        $getPosts = new GetPosts([], 1, null, $wpService, $wpQueryFactory, $postTypeFromSchemaTypeResolver);

        $sortedPosts = $getPosts->sortPosts($posts, 'menu_order', 'asc');

        $this->assertEquals(1, $sortedPosts[0]->menu_order);
        $this->assertEquals(2, $sortedPosts[1]->menu_order);
        $this->assertEquals(3, $sortedPosts[2]->menu_order);
    }

    #[TestDox('sortPosts() sorts posts randomly')]
    public function testSortPostsSortsPostsRandomly() {
        
        $numberOfPosts = 20;
        $posts = array_map(function($i) {
            return $this->getWpPostMock(['ID' => $i]);
        }, range(1, $numberOfPosts));

        $wpService = new FakeWpService(['getTheID' => 1, 'isArchive' => false]);
        $wpQueryFactory = $this->createStub(WpQueryFactoryInterface::class);
        $postTypeFromSchemaTypeResolver = new NullPostTypesFromSchemaTypeResolver();

        $getPosts = new GetPosts([], 1, null, $wpService, $wpQueryFactory, $postTypeFromSchemaTypeResolver);

        $firstSort = $getPosts->sortPosts($posts, 'rand', 'asc');
        $secondSort = $getPosts->sortPosts($posts, 'rand', 'asc');

        $this->assertEqualsCanonicalizing($posts, $firstSort); // Assert that the posts are the same
        $this->assertEqualsCanonicalizing($posts, $secondSort); // Assert that the posts are the same

        $this->assertNotEquals($firstSort, $secondSort); // Assert that the posts are not in the same order
        $this->assertNotEquals($firstSort, $posts); // Assert that the posts are not in the same order
        $this->assertNotEquals($secondSort, $posts); // Assert that the posts are not in the same order
    }

    #[TestDox('sortPosts() does not sort if invalid sort order is provided')]
    public function testSortPostsDoesNotSortIfInvalidSortOrderIsProvided() {
        $posts = [
            $this->getWpPostMock(['menu_order' => 3]),
            $this->getWpPostMock(['menu_order' => 1]),
            $this->getWpPostMock(['menu_order' => 2]),
        ];

        $wpService = new FakeWpService(['getTheID' => 1, 'isArchive' => false]);
        $wpQueryFactory = $this->createStub(WpQueryFactoryInterface::class);
        $postTypeFromSchemaTypeResolver = new NullPostTypesFromSchemaTypeResolver();
        $getPosts = new GetPosts([], 1, null, $wpService, $wpQueryFactory, $postTypeFromSchemaTypeResolver);    

        $sortedPosts = $getPosts->sortPosts($posts, 'foo');

        $this->assertEquals(3, $sortedPosts[0]->menu_order);
        $this->assertEquals(1, $sortedPosts[1]->menu_order);
        $this->assertEquals(2, $sortedPosts[2]->menu_order);
    }

    private function getWpPostMock(array $data = []):WP_Post|MockObject {
        $wpPost = $this->createStub(stdClass::class);
        $wpPost->post_date = $data['post_date'] ?? '';
        $wpPost->post_title = $data['post_title'] ?? '';
        $wpPost->post_modified = $data['post_modified'] ?? '';
        $wpPost->menu_order = $data['menu_order'] ?? 0;
        $wpPost->ID = $data['ID'] ?? 0;
        
        return $wpPost;
    }
}