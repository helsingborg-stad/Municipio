<?php

namespace Municipio\Helper\Term;

use AcfService\Implementations\FakeAcfService;
use Municipio\TestUtils\WpMockFactory;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class TermTests extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $this->assertInstanceOf(Term::class, new Term(new FakeWpService(), new FakeAcfService()));
    }

    /**
     * @testdox getTermColor() returns false if term is empty
     */
    public function testGetTermColorReturnsFalseIfTermIsEmpty()
    {
        $termHelper = new Term(new FakeWpService(), new FakeAcfService());

        $this->assertFalse($termHelper->getTermColor('', 'category'));
        $this->assertFalse($termHelper->getTermColor(0, 'category'));
    }

    /**
     * @testdox getTermColor() returns false if term is not found when passing an ID or slug
     */
    public function testGetTermColorReturnsFalseIfTermIsNotFoundWhenPassingAnIdOrSlug()
    {
        $wpService  = new FakeWpService(['getTermBy' => false]);
        $termHelper = new Term($wpService, new FakeAcfService());

        $this->assertFalse($termHelper->getTermColor(123, 'category'));
        $this->assertFalse($termHelper->getTermColor('non-existing-slug', 'category'));
    }

    /**
     * @testdox getTermColor() returns false if term is found but has no colour and no ancestors
     */
    public function testGetTermColorReturnsFalseIfTermIsFoundButHasNoColour()
    {
        $acfService = new FakeAcfService(['getField' => null]);
        $wpService  = new FakeWpService([
            'getTermBy'    => WpMockFactory::createWpTerm(['term_id' => 123, 'taxonomy' => 'category']),
            'getAncestors' => [],
            'applyFilters' => fn($hook, $value) => $value]);

        $termHelper = new Term($wpService, $acfService);

        $this->assertFalse($termHelper->getTermColor(123, 'category'));
    }

    /**
     * @testdox getTermColor() returns the colour of the term if it has one
     */
    public function testGetTermColorReturnsTheColourOfTheTermIfItHasOne()
    {
        $acfService = new FakeAcfService(['getField' => '#000000']);
        $wpService  = new FakeWpService([
            'getTermBy'    => WpMockFactory::createWpTerm(['term_id' => 123, 'taxonomy' => 'category']),
            'applyFilters' => fn($hook, $value) => $value]);

        $termHelper = new Term($wpService, $acfService);

        $this->assertEquals('#000000', $termHelper->getTermColor(123, 'category'));
    }

    /**
     * @testdox getTermColor() prepends a hash to the colour if it does not have one
     */
    public function testGetTermColorPrependsAHashToTheColourIfItDoesNotHaveOne()
    {
        $acfService = new FakeAcfService(['getField' => '000000']);
        $wpService  = new FakeWpService([
            'getTermBy'    => WpMockFactory::createWpTerm(['term_id' => 123, 'taxonomy' => 'category']),
            'applyFilters' => fn($hook, $value) => $value]);

        $termHelper = new Term($wpService, $acfService);

        $this->assertEquals('#000000', $termHelper->getTermColor(123, 'category'));
    }

    /**
     * @testdox getAncestorTermColor() returns false if term has no ancestors
     */
    public function testGetAncestorTermColorReturnsFalseIfTermHasNoAncestors()
    {
        $wpService  = new FakeWpService(['getAncestors' => []]);
        $termHelper = new Term($wpService, new FakeAcfService());

        $this->assertFalse($termHelper->getAncestorTermColor(WpMockFactory::createWpTerm(['term_id' => 123, 'taxonomy' => 'category'])));
    }

    /**
     * @testdox getAncestorTermColor() returns false if no ancestor has a colour
     */
    public function testGetAncestorTermColorReturnsFalseIfNoAncestorHasAColour()
    {
        $acfService = new FakeAcfService(['getField' => null]);
        $wpService  = new FakeWpService([ 'getAncestors' => [1, 2, 3]]);

        $termHelper = new Term($wpService, $acfService);

        $this->assertFalse($termHelper->getAncestorTermColor(WpMockFactory::createWpTerm(['term_id' => 123, 'taxonomy' => 'category'])));
    }

    /**
     * @testdox getAncestorTermColor() returns the colour of the first ancestor that has a colour
     */
    public function testGetAncestorTermColorReturnsTheColourOfTheFirstAncestorThatHasAColour()
    {
        $acfService = new FakeAcfService(['getField' => '#000000']);
        $wpService  = new FakeWpService([ 'getAncestors' => [1, 2, 3]]);

        $termHelper = new Term($wpService, $acfService);

        $this->assertEquals('#000000', $termHelper->getAncestorTermColor(WpMockFactory::createWpTerm(['term_id' => 123, 'taxonomy' => 'category'])));
    }

    /**
     * @testdox getTermIcon() returns false if term can not be found by ID or slug
     */
    public function testGetTermIconReturnsFalseIfTermCanNotBeFoundByIdOrSlug()
    {
        $wpService  = new FakeWpService(['getTermBy' => false]);
        $termHelper = new Term($wpService, new FakeAcfService());

        $this->assertFalse($termHelper->getTermIcon(123, 'category'));
        $this->assertFalse($termHelper->getTermIcon('non-existing-slug', 'category'));
    }

    /**
     * @testdox getTermIcon() returns false if term has no icon
     */
    public function testGetTermIconReturnsFalseIfTermHasNoIcon()
    {
        $acfService = new FakeAcfService(['getField' => null]);
        $wpService  = new FakeWpService([
            'getTermBy'    => WpMockFactory::createWpTerm(['term_id' => 123, 'taxonomy' => 'category']),
            'applyFilters' => fn($hook, $value) => $value]);

        $termHelper = new Term($wpService, $acfService);

        $this->assertFalse($termHelper->getTermIcon(123, 'category'));
    }

    /**
     * @testdox getTermIcon() returns the icon of the term if it has one
     */
    public function testGetTermIconReturnsTheIconOfTheTermIfItHasOne()
    {
        $acfService = new FakeAcfService(['getField' => ['type' => 'icon', 'material_icon' => 'home']]);
        $wpService  = new FakeWpService([
            'getTermBy'    => WpMockFactory::createWpTerm(['term_id' => 123, 'taxonomy' => 'category']),
            'applyFilters' => fn($hook, $value) => $value]);

        $termHelper = new Term($wpService, $acfService);

        $this->assertEquals(['src' => 'home', 'type' => 'icon'], $termHelper->getTermIcon(123, 'category'));
    }

    /**
     * @testdox getTermIcon() returns the SVG icon of the term if it has one
     */
    public function testGetTermIconReturnsTheSvgIconOfTheTermIfItHasOne()
    {
        $acfService = new FakeAcfService([
            'getField' => [
                'type'     => 'svg',
                'svg_icon' => '<svg></svg>',
                'svg'      => ['ID' => 1, 'description' => 'description']]
            ]);
        $wpService  = new FakeWpService([
            'getTermBy'               => WpMockFactory::createWpTerm(['term_id' => 123, 'taxonomy' => 'category']),
            'wpGetAttachmentImageUrl' => 'attachment-url',
            'applyFilters'            => fn($hook, $value) => $value]);

        $termHelper = new Term($wpService, $acfService);
        $icon       = $termHelper->getTermIcon(123, 'category');

        $this->assertEquals('attachment-url', $icon['src']);
        $this->assertEquals('svg', $icon['type']);
        $this->assertEquals('description', $icon['description']);
        $this->assertEquals('description', $icon['alt']);
    }
}
