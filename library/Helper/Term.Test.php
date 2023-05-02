<?php

class TermTest extends WP_UnitTestCase
{

    protected $taxonomy = 'tests_tax';

    public function set_up()
    {
        parent::set_up();
        register_taxonomy($this->taxonomy, 'post');
    }

    public function testClassIsDefined()
    {
        $this->assertTrue(class_exists(\Municipio\Helper\Term::class));
    }

    public function testGetTermColourReturnsFalseIfInputParamsAreInvalid()
    {
        $sut = new \Municipio\Helper\Term();

        $result = $sut->getTermColour('', '');
        $this->assertFalse($result);
    }

    public function testGetTermColourReturnsColourIfIsset()
    {
        $colour = '#FF0000';
        $term = wp_insert_term('Foo', $this->taxonomy);
        update_field('colour', $colour, 'term_' . $term['term_id']);
        $sut = new \Municipio\Helper\Term();

        $result = $sut->getTermColour($term['term_id'], $this->taxonomy);
        $this->assertEquals($colour, $result);
    }

    public function testGetTermColourReturnsParentTermColourAsFallback()
    {
        $colour = '#FF0000';
        $parentTerm = wp_insert_term('Foo', $this->taxonomy);
        $term = wp_insert_term('Bar', $this->taxonomy, ['parent' => $parentTerm['term_id']]);

        update_field('colour', $colour, 'term_' . $parentTerm['term_id']);
        $sut = new \Municipio\Helper\Term();

        $result = $sut->getTermColour($term['term_id'], $this->taxonomy);
        $this->assertEquals($colour, $result);
    }
}
