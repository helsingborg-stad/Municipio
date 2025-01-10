<?php

namespace Municipio\PostObject\Icon\Resolvers;

use Municipio\Helper\Term\Contracts\GetTermColor;
use Municipio\Helper\Term\Contracts\GetTermIcon;
use Municipio\PostObject\Icon\IconInterface;
use Municipio\PostObject\PostObjectInterface;
use Municipio\TestUtils\WpMockFactory;
use PHPUnit\Framework\TestCase;
use WP_Term;
use WpService\Implementations\FakeWpService;

class TermIconResolverTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $postObject    = $this->createMock(PostObjectInterface::class);
        $wpService     = new FakeWpService();
        $innerResolver = $this->createMock(IconResolverInterface::class);

        $resolver = new TermIconResolver($postObject, $wpService, $this->getTermHelper(), $innerResolver);

        $this->assertInstanceOf(TermIconResolver::class, $resolver);
    }

    /**
     * @testdox resolve() calls inner resolver if postObject post type has no taxonomies
     */
    public function testResolveCallsInnerResolverIfPostObjectPostTypeHasNoTaxonomies()
    {
        $postObject    = $this->createMock(PostObjectInterface::class);
        $wpService     = new FakeWpService(['getObjectTaxonomies' => []]);
        $innerResolver = $this->createMock(IconResolverInterface::class);
        $innerResolver->expects($this->once())->method('resolve');

        $resolver = new TermIconResolver($postObject, $wpService, $this->getTermHelper(), $innerResolver);

        $resolver->resolve();
    }

    /**
     * @testdox resolve() calls inner resolver if postObject has terms
     */
    public function testResolveCallsInnerResolverIfPostObjectHasTerms()
    {
        $postObject    = $this->createMock(PostObjectInterface::class);
        $wpService     = new FakeWpService([
            'getObjectTaxonomies' => ['category'],
            'getTheTerms'         => []]);
        $innerResolver = $this->createMock(IconResolverInterface::class);
        $innerResolver->expects($this->once())->method('resolve');

        $resolver = new TermIconResolver($postObject, $wpService, $this->getTermHelper(), $innerResolver);

        $resolver->resolve();
    }

    /**
     * @testdox resolve() calls inner resolver if term has no icon
     */
    public function testResolveReturnsNullIfTermHasNoIcon()
    {
        $postObject    = $this->createMock(PostObjectInterface::class);
        $wpService     = new FakeWpService([
            'getObjectTaxonomies' => ['category'],
            'getTheTerms'         => [WpMockFactory::createWpTerm()]]);
        $innerResolver = $this->createMock(IconResolverInterface::class);
        $innerResolver->expects($this->once())->method('resolve');

        $resolver = new TermIconResolver($postObject, $wpService, $this->getTermHelper(), $innerResolver);

        $resolver->resolve();
    }

    /**
     * @testdox resolve() returns an IconInterface if postObject term has an icon
     */
    public function testResolveReturnsIconInterfaceIfTermHasIcon()
    {
        $postObject    = $this->createMock(PostObjectInterface::class);
        $wpService     = new FakeWpService([
            'getObjectTaxonomies' => ['category'],
            'getTheTerms'         => [WpMockFactory::createWpTerm()]]);
        $termHelper    = $this->getTermHelper([
            'getTermIcon'  => ['src' => 'testIcon', 'type' => 'testType'],
            'getTermColor' => 'testColor'
        ]);
        $innerResolver = $this->createMock(IconResolverInterface::class);

        $resolver = new TermIconResolver($postObject, $wpService, $termHelper, $innerResolver);

        $this->assertInstanceOf(IconInterface::class, $resolver->resolve());
    }

    /**
     * @testdox resolve() returns an IconInterface with correct values
     */
    public function testResolveReturnsIconInterfaceWithCorrectValues()
    {
        $postObject    = $this->createMock(PostObjectInterface::class);
        $wpService     = new FakeWpService([
            'getObjectTaxonomies' => ['category'],
            'getTheTerms'         => [WpMockFactory::createWpTerm()]]);
        $termHelper    = $this->getTermHelper([
            'getTermIcon'  => ['src' => 'testIcon', 'type' => 'testType'],
            'getTermColor' => 'testColor'
        ]);
        $innerResolver = $this->createMock(IconResolverInterface::class);

        $resolver = new TermIconResolver($postObject, $wpService, $termHelper, $innerResolver);
        $icon     = $resolver->resolve();

        $this->assertEquals('testIcon', $icon->getIcon());
        $this->assertEquals('testColor', $icon->getCustomColor());
    }

    private function getTermHelper(array $returnValues = []): GetTermIcon&GetTermColor
    {
        return new class ($returnValues) implements GetTermIcon, GetTermColor {
            public static $getTermIconCallCount = 0;
            public function __construct(private array $returnValues)
            {
            }

            public function getTermIcon(int|string|\WP_Term $term, string $taxonomy = ''): array|false
            {
                self::$getTermIconCallCount++;
                return $this->returnValues['getTermIcon'] ?? false;
            }

            public function getTermColor(int|string|\WP_Term $term, string $taxonomy = ''): false|string
            {
                return $this->returnValues['getTermColor'] ?? false;
            }
        };
    }
}
