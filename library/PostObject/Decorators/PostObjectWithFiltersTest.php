<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\PostObjectInterface;
use Municipio\Schema\Schema;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class PostObjectWithFiltersTest extends TestCase
{
    private function getPostObject(): PostObjectInterface|MockObject
    {
        return $this->createMock(PostObjectInterface::class);
    }

    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated(): void
    {
        $this->assertInstanceOf(
            PostObjectWithFilters::class,
            new PostObjectWithFilters($this->getPostObject(), new FakeWpService([]))
        );
    }

    #[TestDox('getId applies filter and returns filtered value')]
    public function testGetIdAppliesFilter(): void
    {
        $postObject = $this->getPostObject();
        $postObject->method('getId')->willReturn(1);

        $sut = new PostObjectWithFilters($postObject, new FakeWpService([
            'applyFilters' => fn($hook, $value) => $hook === 'Municipio/PostObject/getId' ? 99 : $value,
        ]));

        $this->assertSame(99, $sut->getId());
    }

    #[TestDox('getTitle applies filter with post object as context')]
    public function testGetTitleAppliesFilterWithPostObject(): void
    {
        $postObject = $this->getPostObject();
        $postObject->method('getTitle')->willReturn('Original Title');

        $sut = new PostObjectWithFilters($postObject, new FakeWpService([
            'applyFilters' => function ($hook, $value, $passedPostObject) use ($postObject) {
                if ($hook === 'Municipio/PostObject/getTitle' && $passedPostObject === $postObject) {
                    return 'Filtered Title';
                }
                return $value;
            },
        ]));

        $this->assertSame('Filtered Title', $sut->getTitle());
    }

    #[TestDox('getContent applies filter and returns filtered value')]
    public function testGetContentAppliesFilter(): void
    {
        $postObject = $this->getPostObject();
        $postObject->method('getContent')->willReturn('<p>Original</p>');

        $sut = new PostObjectWithFilters($postObject, new FakeWpService([
            'applyFilters' => fn($hook, $value) => $hook === 'Municipio/PostObject/getContent' ? '<p>Filtered</p>' : $value,
        ]));

        $this->assertSame('<p>Filtered</p>', $sut->getContent());
    }

    #[TestDox('getExcerpt applies filter and returns filtered value')]
    public function testGetExcerptAppliesFilter(): void
    {
        $postObject = $this->getPostObject();
        $postObject->method('getExcerpt')->willReturn('Original excerpt');

        $sut = new PostObjectWithFilters($postObject, new FakeWpService([
            'applyFilters' => fn($hook, $value) => $hook === 'Municipio/PostObject/getExcerpt' ? 'Filtered excerpt' : $value,
        ]));

        $this->assertSame('Filtered excerpt', $sut->getExcerpt());
    }

    #[TestDox('getContentHeadings applies filter and returns filtered value')]
    public function testGetContentHeadingsAppliesFilter(): void
    {
        $postObject = $this->getPostObject();
        $postObject->method('getContentHeadings')->willReturn([['level' => 1, 'text' => 'Original']]);

        $filtered = [['level' => 2, 'text' => 'Filtered']];
        $sut      = new PostObjectWithFilters($postObject, new FakeWpService([
            'applyFilters' => fn($hook, $value) => $hook === 'Municipio/PostObject/getContentHeadings' ? $filtered : $value,
        ]));

        $this->assertSame($filtered, $sut->getContentHeadings());
    }

    #[TestDox('getPermalink applies filter and returns filtered value')]
    public function testGetPermalinkAppliesFilter(): void
    {
        $postObject = $this->getPostObject();
        $postObject->method('getPermalink')->willReturn('https://example.com/original');

        $sut = new PostObjectWithFilters($postObject, new FakeWpService([
            'applyFilters' => fn($hook, $value) => $hook === 'Municipio/PostObject/getPermalink' ? 'https://example.com/filtered' : $value,
        ]));

        $this->assertSame('https://example.com/filtered', $sut->getPermalink());
    }

    #[TestDox('getCommentCount applies filter and returns filtered value')]
    public function testGetCommentCountAppliesFilter(): void
    {
        $postObject = $this->getPostObject();
        $postObject->method('getCommentCount')->willReturn(3);

        $sut = new PostObjectWithFilters($postObject, new FakeWpService([
            'applyFilters' => fn($hook, $value) => $hook === 'Municipio/PostObject/getCommentCount' ? 7 : $value,
        ]));

        $this->assertSame(7, $sut->getCommentCount());
    }

    #[TestDox('getPostType applies filter and returns filtered value')]
    public function testGetPostTypeAppliesFilter(): void
    {
        $postObject = $this->getPostObject();
        $postObject->method('getPostType')->willReturn('post');

        $sut = new PostObjectWithFilters($postObject, new FakeWpService([
            'applyFilters' => fn($hook, $value) => $hook === 'Municipio/PostObject/getPostType' ? 'page' : $value,
        ]));

        $this->assertSame('page', $sut->getPostType());
    }

    #[TestDox('getBlogId applies filter and returns filtered value')]
    public function testGetBlogIdAppliesFilter(): void
    {
        $postObject = $this->getPostObject();
        $postObject->method('getBlogId')->willReturn(1);

        $sut = new PostObjectWithFilters($postObject, new FakeWpService([
            'applyFilters' => fn($hook, $value) => $hook === 'Municipio/PostObject/getBlogId' ? 2 : $value,
        ]));

        $this->assertSame(2, $sut->getBlogId());
    }

    #[TestDox('getIcon applies filter and returns filtered value')]
    public function testGetIconAppliesFilter(): void
    {
        $postObject = $this->getPostObject();
        $postObject->method('getIcon')->willReturn(null);

        $filterApplied = false;
        $sut           = new PostObjectWithFilters($postObject, new FakeWpService([
            'applyFilters' => function ($hook, $value) use (&$filterApplied) {
                if ($hook === 'Municipio/PostObject/getIcon') {
                    $filterApplied = true;
                }
                return $value;
            },
        ]));

        $sut->getIcon();

        $this->assertTrue($filterApplied);
    }

    #[TestDox('getPublishedTime passes gmt flag to inner object and filter')]
    public function testGetPublishedTimePassesGmtFlag(): void
    {
        $postObject = $this->getPostObject();
        $postObject->method('getPublishedTime')->with(true)->willReturn(1000);

        $sut = new PostObjectWithFilters($postObject, new FakeWpService([
            'applyFilters' => function ($hook, $value, $passedPostObject, $gmt) {
                if ($hook === 'Municipio/PostObject/getPublishedTime' && $gmt === true) {
                    return 2000;
                }
                return $value;
            },
        ]));

        $this->assertSame(2000, $sut->getPublishedTime(true));
    }

    #[TestDox('getModifiedTime passes gmt flag to inner object and filter')]
    public function testGetModifiedTimePassesGmtFlag(): void
    {
        $postObject = $this->getPostObject();
        $postObject->method('getModifiedTime')->with(false)->willReturn(1000);

        $sut = new PostObjectWithFilters($postObject, new FakeWpService([
            'applyFilters' => function ($hook, $value, $passedPostObject, $gmt) {
                if ($hook === 'Municipio/PostObject/getModifiedTime' && $gmt === false) {
                    return 3000;
                }
                return $value;
            },
        ]));

        $this->assertSame(3000, $sut->getModifiedTime(false));
    }

    #[TestDox('getArchiveDateTimestamp applies filter and returns filtered value')]
    public function testGetArchiveDateTimestampAppliesFilter(): void
    {
        $postObject = $this->getPostObject();
        $postObject->method('getArchiveDateTimestamp')->willReturn(12345);

        $sut = new PostObjectWithFilters($postObject, new FakeWpService([
            'applyFilters' => fn($hook, $value) => $hook === 'Municipio/PostObject/getArchiveDateTimestamp' ? 99999 : $value,
        ]));

        $this->assertSame(99999, $sut->getArchiveDateTimestamp());
    }

    #[TestDox('getArchiveDateFormat applies filter and returns filtered value')]
    public function testGetArchiveDateFormatAppliesFilter(): void
    {
        $postObject = $this->getPostObject();
        $postObject->method('getArchiveDateFormat')->willReturn('Y-m-d');

        $sut = new PostObjectWithFilters($postObject, new FakeWpService([
            'applyFilters' => fn($hook, $value) => $hook === 'Municipio/PostObject/getArchiveDateFormat' ? 'd/m/Y' : $value,
        ]));

        $this->assertSame('d/m/Y', $sut->getArchiveDateFormat());
    }

    #[TestDox('getSchemaProperty passes property name to filter')]
    public function testGetSchemaPropertyPassesPropertyNameToFilter(): void
    {
        $postObject = $this->getPostObject();
        $postObject->method('getSchemaProperty')->with('name')->willReturn('Original Name');

        $sut = new PostObjectWithFilters($postObject, new FakeWpService([
            'applyFilters' => function ($hook, $value, $passedPostObject, $property) {
                if ($hook === 'Municipio/PostObject/getSchemaProperty' && $property === 'name') {
                    return 'Filtered Name';
                }
                return $value;
            },
        ]));

        $this->assertSame('Filtered Name', $sut->getSchemaProperty('name'));
    }

    #[TestDox('getSchema applies filter and returns filtered schema')]
    public function testGetSchemaAppliesFilter(): void
    {
        $originalSchema = Schema::thing()->name('Original');
        $filteredSchema = Schema::thing()->name('Filtered');

        $postObject = $this->getPostObject();
        $postObject->method('getSchema')->willReturn($originalSchema);

        $sut = new PostObjectWithFilters($postObject, new FakeWpService([
            'applyFilters' => fn($hook, $value) => $hook === 'Municipio/PostObject/getSchema' ? $filteredSchema : $value,
        ]));

        $this->assertSame($filteredSchema, $sut->getSchema());
    }

    #[TestDox('getTerms passes taxonomies to inner object and filter')]
    public function testGetTermsPassesTaxonomiesToFilter(): void
    {
        $postObject    = $this->getPostObject();
        $taxonomies    = ['category', 'post_tag'];
        $filteredTerms = [(object)['name' => 'Filtered Term']];

        $postObject->method('getTerms')->with($taxonomies)->willReturn([]);

        $sut = new PostObjectWithFilters($postObject, new FakeWpService([
            'applyFilters' => function ($hook, $value, $passedPostObject, $passedTaxonomies) use ($filteredTerms, $taxonomies) {
                if ($hook === 'Municipio/PostObject/getTerms' && $passedTaxonomies === $taxonomies) {
                    return $filteredTerms;
                }
                return $value;
            },
        ]));

        $this->assertSame($filteredTerms, $sut->getTerms($taxonomies));
    }

    #[TestDox('getImage passes width and height to filter')]
    public function testGetImagePassesDimensionsToFilter(): void
    {
        $postObject = $this->getPostObject();
        $postObject->method('getImage')->with(300, 200)->willReturn(null);

        $filterApplied = false;
        $sut           = new PostObjectWithFilters($postObject, new FakeWpService([
            'applyFilters' => function ($hook, $value, $passedPostObject, $width, $height) use (&$filterApplied) {
                if ($hook === 'Municipio/PostObject/getImage' && $width === 300 && $height === 200) {
                    $filterApplied = true;
                }
                return $value;
            },
        ]));

        $sut->getImage(300, 200);

        $this->assertTrue($filterApplied);
    }

    #[TestDox('__get passes key to filter and returns filtered value')]
    public function testMagicGetPassesKeyToFilter(): void
    {
        $postObject = $this->getPostObject();
        $postObject->method('__get')->with('customField')->willReturn('original value');

        $sut = new PostObjectWithFilters($postObject, new FakeWpService([
            'applyFilters' => function ($hook, $value, $passedPostObject, $key) {
                if ($hook === 'Municipio/PostObject/__get' && $key === 'customField') {
                    return 'filtered value';
                }
                return $value;
            },
        ]));

        $this->assertSame('filtered value', $sut->__get('customField'));
    }
}
