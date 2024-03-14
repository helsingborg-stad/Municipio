<?php

namespace Municipio\Tests\Navigation\Builder;

use PHPUnit\Framework\TestCase;
use Municipio\Navigation\Builders\BreadCrumbsBuilder;
use Municipio\Navigation\MenuItem;

class BreadCrumbsBuilderTest extends TestCase {
    public function testEmptyMenuReturnsEmptyArray() {
        $BreadCrumbsBuilder = new BreadCrumbsBuilder([]);
        $this->assertIsArray($BreadCrumbsBuilder->build());
        $this->assertEmpty($BreadCrumbsBuilder->build());
    }

    public function testMenuWithoutCurrentItemReturnsEmptyArray() {
        $menuItems = [
            new MenuItem(1, 'Home', '/', null, false),
            new MenuItem(2, 'About', '/about', null, false),
            new MenuItem(3, 'Contact', '/contact', null, false),
        ];
        $BreadCrumbsBuilder = new BreadCrumbsBuilder($menuItems);
        $result = $BreadCrumbsBuilder->build();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testMenuWithCurrentItemReturnsArrayWithOneItem() {
        $current = new MenuItem(3, 'Contact', '/contact', null, true);
        $menuItems = [
            new MenuItem(1, 'Home', '/', null, false),
            new MenuItem(2, 'About', '/about', null, false),
            $current
        ];
        $BreadCrumbsBuilder = new BreadCrumbsBuilder($menuItems);
        $result = $BreadCrumbsBuilder->build();

        $this->assertCount(1, $result);
        $this->assertEquals($current, $result[0]);
    }

    public function testMenuWithCurrentItemAndParentReturnsArrayWithTwoItems() {
        $current = new MenuItem(3, 'Contact', '/contact', 2, true);
        $parent = new MenuItem(2, 'About', '/about', null, false);
        $menuItems = [
            new MenuItem(1, 'Home', '/', null, false),
            $parent,
            $current
        ];
        $BreadCrumbsBuilder = new BreadCrumbsBuilder($menuItems);
        $result = $BreadCrumbsBuilder->build();

        $this->assertCount(2, $result);
        $this->assertEquals($parent, $result[0]);
        $this->assertEquals($current, $result[1]);
    }
    
    public function testMenuWithCurrentItemAndParentAndGrandParentReturnsArrayWithThreeItems() {
        $current = new MenuItem(3, 'Contact', '/contact', 2, true);
        $parent = new MenuItem(2, 'About', '/about', 1, false);
        $grandParent = new MenuItem(1, 'Home', '/', null, false);
        $menuItems = [
            $grandParent,
            $parent,
            $current
        ];
        $BreadCrumbsBuilder = new BreadCrumbsBuilder($menuItems);
        $result = $BreadCrumbsBuilder->build();

        $this->assertCount(3, $result);
        $this->assertEquals($grandParent, $result[0]);
        $this->assertEquals($parent, $result[1]);
        $this->assertEquals($current, $result[2]);
    }

    public function testMenuWithCurrentItemAndParentAndGrandParentReturnsArray() {
      
      $menuItems = [
        new MenuItem(3, 'Contact', '/contact', 1, true),
        new MenuItem(2, 'About', '/about', 3, false),
        new MenuItem(1, 'Home', '/', 2, false)
      ];
      
      $BreadCrumbsBuilder = new BreadCrumbsBuilder($menuItems);
      $result = $BreadCrumbsBuilder->build();

      $this->assertCount(3, $result);
  }
}
