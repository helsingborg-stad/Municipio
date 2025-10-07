<?php

namespace Municipio\Helper\Term;

use AcfService\Implementations\FakeAcfService;
use PHPUnit\Framework\TestCase;
use WP_Error;
use WP_Term;
use WpService\Implementations\FakeWpService;

class TermTest extends TestCase
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
        $acfService     = new FakeAcfService(['getField' => null]);
        $term           = new WP_Term([]);
        $term->term_id  = 123;
        $term->taxonomy = 'category';
        $wpService      = new FakeWpService([
            'getTermBy'    => $term,
            'getAncestors' => [],
            'applyFilters' => fn($hook, $value) => $value]);

        $termHelper = new Term($wpService, $acfService);

        $this->assertFalse($termHelper->getTermColor(123, 'category'));
    }

    /**
     * @testdox getTermColor() returns the colour of the term if it has one
     * @runInSeparateProcess
     */
    public function testGetTermColorReturnsTheColourOfTheTermIfItHasOne()
    {
        $acfService     = new FakeAcfService(['getField' => '#000000']);
        $term           = new WP_Term([]);
        $term->term_id  = 123;
        $term->taxonomy = 'category';
        $wpService      = new FakeWpService([
            'getTermBy'    => $term,
            'applyFilters' => fn($hook, $value) => $value]);

        $termHelper     = new Term($wpService, $acfService);

        $this->assertEquals('#000000', $termHelper->getTermColor(123, 'category'));
    }

    /**
     * @testdox getTermColor() prepends a hash to the colour if it does not have one
     * @runInSeparateProcess
     */
    public function testGetTermColorPrependsAHashToTheColourIfItDoesNotHaveOne()
    {
        $acfService     = new FakeAcfService(['getField' => '000000']);
        $term           = new WP_Term([]);
        $term->term_id  = 123;
        $term->taxonomy = 'category';
        $wpService      = new FakeWpService([
            'getTermBy'    => $term,
            'applyFilters' => fn($hook, $value) => $value]);

        $termHelper     = new Term($wpService, $acfService);

        $this->assertEquals('#000000', $termHelper->getTermColor(123, 'category'));
    }

    /**
     * @testdox getAncestorTermColor() returns false if term has no ancestors
     */
    public function testGetAncestorTermColorReturnsFalseIfTermHasNoAncestors()
    {
        $wpService      = new FakeWpService(['getAncestors' => []]);
        $termHelper     = new Term($wpService, new FakeAcfService());
        $term           = new WP_Term([]);
        $term->term_id  = 123;
        $term->taxonomy = 'category';

        $this->assertFalse($termHelper->getAncestorTermColor($term));
    }

    /**
     * @testdox getAncestorTermColor() returns false if no ancestor has a colour
     */
    public function testGetAncestorTermColorReturnsFalseIfNoAncestorHasAColour()
    {
        $acfService     = new FakeAcfService(['getField' => null]);
        $wpService      = new FakeWpService([ 'getAncestors' => [1, 2, 3]]);
        $term           = new WP_Term([]);
        $term->term_id  = 123;
        $term->taxonomy = 'category';

        $termHelper = new Term($wpService, $acfService);

        $this->assertFalse($termHelper->getAncestorTermColor($term));
    }

    /**
     * @testdox getAncestorTermColor() returns the colour of the first ancestor that has a colour
     */
    public function testGetAncestorTermColorReturnsTheColourOfTheFirstAncestorThatHasAColour()
    {
        $acfService     = new FakeAcfService(['getField' => '#000000']);
        $wpService      = new FakeWpService([ 'getAncestors' => [1, 2, 3]]);
        $term           = new WP_Term([]);
        $term->term_id  = 123;
        $term->taxonomy = 'category';

        $termHelper = new Term($wpService, $acfService);


        $this->assertEquals('#000000', $termHelper->getAncestorTermColor($term));
    }

    /**
     * @testdox getTermIcon() returns false if term can not be found by ID or slug
     * @runInSeparateProcess
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
     * @runInSeparateProcess
     */
    public function testGetTermIconReturnsFalseIfTermHasNoIcon()
    {
        $acfService     = new FakeAcfService(['getField' => null]);
        $term           = new WP_Term([]);
        $term->term_id  = 123;
        $term->taxonomy = 'category';
        $wpService      = new FakeWpService([
            'getTermBy'    => $term,
            'applyFilters' => fn($hook, $value) => $value]);

        $termHelper     = new Term($wpService, $acfService);

        $this->assertFalse($termHelper->getTermIcon(123, 'category'));
    }

    /**
     * @testdox getTermIcon() returns the icon of the term if it has one
     * @runInSeparateProcess
     */
    public function testGetTermIconReturnsTheIconOfTheTermIfItHasOne()
    {
        $acfService     = new FakeAcfService(['getField' => ['type' => 'icon', 'material_icon' => 'home']]);
        $term           = new WP_Term([]);
        $term->term_id  = 123;
        $term->taxonomy = 'category';
        $wpService      = new FakeWpService([
            'getTermBy'    => $term,
            'applyFilters' => fn($hook, $value) => $value]);

        $termHelper     = new Term($wpService, $acfService);

        $this->assertEquals(['src' => 'home', 'type' => 'icon'], $termHelper->getTermIcon(123, 'category'));
    }

    /**
     * @testdox getTermIcon() returns the SVG icon of the term if it has one
     * @runInSeparateProcess
     */
    public function testGetTermIconReturnsTheSvgIconOfTheTermIfItHasOne()
    {
        $term           = new WP_Term([]);
        $term->term_id  = 123;
        $term->taxonomy = 'category';
        $acfService     = new FakeAcfService([
            'getField' => [
                'type'     => 'svg',
                'svg_icon' => '<svg></svg>',
                'svg'      => ['ID' => 1, 'description' => 'description']]
            ]);
        $wpService      = new FakeWpService([
            'getTermBy'               => $term,
            'wpGetAttachmentImageUrl' => 'attachment-url',
            'applyFilters'            => fn($hook, $value) => $value]);

        $termHelper     = new Term($wpService, $acfService);
        $icon           = $termHelper->getTermIcon(123, 'category');

        $this->assertEquals('attachment-url', $icon['src']);
        $this->assertEquals('svg', $icon['type']);
        $this->assertEquals('description', $icon['description']);
        $this->assertEquals('description', $icon['alt']);
    }

    /**
     * @testdox getTermIcon() returns cached result if term has been checked before
     */
    public function testGetTermIconReturnsCachedResultIfTermHasBeenCheckedBefore()
    {
        $term           = new WP_Term([]);
        $term->term_id  = 123;
        $term->taxonomy = 'category';
        $acfService     = new FakeAcfService(['getField' => ['type' => 'icon', 'material_icon' => 'home']]);
        $wpService      = new FakeWpService([
            'getTermBy'    => $term,
            'applyFilters' => fn($hook, $value) => $value]);

        $termHelper     = new Term($wpService, $acfService);

        $this->assertEquals(['src' => 'home', 'type' => 'icon'], $termHelper->getTermIcon(123, 'category'));
        $this->assertEquals(['src' => 'home', 'type' => 'icon'], $termHelper->getTermIcon(123, 'category'));

        $this->assertCount(1, $acfService->methodCalls['getField']);
    }

    /**
     * @testdox createOrGetTermIdFromString() returns termId if term already exists
     */
    public function testCreateOrGetTermIdFromStringReturnsTermIdIfTermAlreadyExists()
    {

        $term           = new WP_Term([]);
        $term->term_id  = 123;
        $term->taxonomy = 'category';
        $wpService      = new FakeWpService(['getTermBy' => $term]);
        $termHelper     = new Term($wpService, new FakeAcfService());

        $this->assertEquals(123, $termHelper->createOrGetTermIdFromString('term', 'category'));
    }

    /**
     * @testdox createOrGetTermIdFromString() returns null if term could no be created
     */
    public function testCreateOrGetTermIdFromStringReturnsNullIfTermCouldNotBeCreated()
    {
        $wpService  = new FakeWpService([
            'getTermBy'    => false,
            'wpInsertTerm' => new WP_Error(),
            'isWpError'    => true]);
        $termHelper = new Term($wpService, new FakeAcfService());

        $this->assertNull($termHelper->createOrGetTermIdFromString('term', 'category'));
    }

    /**
     * @testdox createOrGetTermIdFromString() returns termId if term was created
     */
    public function testCreateOrGetTermIdFromStringReturnsTermIdIfTermWasCreated()
    {
        $wpService  = new FakeWpService([
            'getTermBy'    => false,
            'wpInsertTerm' => ['term_id' => 123],
            'isWpError'    => false]);
        $termHelper = new Term($wpService, new FakeAcfService());

        $this->assertEquals(123, $termHelper->createOrGetTermIdFromString('term', 'category'));
    }
}
