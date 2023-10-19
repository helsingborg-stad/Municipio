<?php

use Google\Service\Reseller\Seats;
use Kirki\Compatibility\Kirki;
use Municipio\Customizer\Sections\Search;
use Municipio\Upgrade;

class SearchTest extends WP_UnitTestCase
{
    public function set_up()
    {
        parent::set_up();
        wp_set_current_user(self::factory()->user->create(array('role' => 'administrator')));
    }

    public function testClassIsDefined()
    {
        $search = new Search('123');
        $this->assertInstanceOf(Search::class, $search);
    }

    public function testGetSearchDisplayOptionsContainsExpectedValues()
    {
        $search = new Search('123');
        $this->assertArrayHasKey('hero', $search->getSearchDisplayOptions());
        $this->assertArrayHasKey('header_sub', $search->getSearchDisplayOptions());
        $this->assertArrayHasKey('header', $search->getSearchDisplayOptions());
        $this->assertArrayHasKey('mainmenu', $search->getSearchDisplayOptions());
        $this->assertArrayHasKey('mobile', $search->getSearchDisplayOptions());
        $this->assertArrayHasKey('mega_menu', $search->getSearchDisplayOptions());
        $this->assertArrayHasKey('quicklinks', $search->getSearchDisplayOptions());
    }

    public function testGetSearchDisplayFieldAttributesReturnsExpected()
    {
        $search = new Search('123');
        $attributes = $search->getSearchDisplayFieldAttributes('123');

        $this->assertEquals('multicheck', $attributes['type']);
    }

    public function testGetSearchFormShapeFieldAttributesReturnsArray()
    {
        $search = new Search('123');
        $this->assertIsArray($search->getSearchFormShapeFieldAttributes('123'));
    }

    public function testGetSearchFormShapeOptionsReturnsArray()
    {
        $search = new Search('123');
        $this->assertIsArray($search->getSearchFormShapeOptions());
    }

    public function testGetSearchFormShapeOptionsAreNumericOrEmptyString()
    {
        $search = new Search('123');
        $options = $search->getSearchFormShapeOptions();
        $keys = array_keys($options);

        $this->assertEquals(2, sizeof($keys));
        $this->assertIsString($keys[0]);
        $this->assertEmpty($keys[0]);
        $this->assertIsNumeric($keys[1]);
    }

    public function testGetSearchFormShapeDefaultValueReturnsString()
    {
        $search = new Search('123');
        $this->assertIsString($search->getSearchFormShapeDefaultValue());
    }

    public function testSearchFormShapeDefaultValueIsInOptions()
    {
        $search = new Search('123');
        $this->assertArrayHasKey($search->getSearchFormShapeDefaultValue(), $search->getSearchFormShapeOptions());
    }

    public function testHeroSearchPositionOptionIsRegisteredWithDefaultValue()
    {
        $this->assertEquals('centered', Kirki::get_option('hero_search_position'));
    }

    public function testHeroSearchFormShapeOptionIsRegisteredWithDefaultValue()
    {
        $search = new Search('123');
        $defaultValue = $search->getSearchFormShapeDefaultValue();
        $this->assertEquals($defaultValue, Kirki::get_option('search_form_shape'));
    }

    public function testHeroSearchDisplayOptionIsRegisteredWithDefaultValue()
    {
        $this->assertEquals([], Kirki::get_option('search_display'));
    }
}
