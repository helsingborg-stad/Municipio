<?php

namespace Municipio\Controller\SingularEvent;

use PHPUnit\Framework\TestCase;

class PriceListItemTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated()
    {
        $priceListItem = new PriceListItem('name', 'price');
        $this->assertInstanceOf(PriceListItem::class, $priceListItem);
    }

    #[TestDox('getName returns name')]
    public function testGetNameReturnsName()
    {
        $priceListItem = new PriceListItem('testname', 'price');
        $this->assertEquals('testname', $priceListItem->getName());
    }

    #[TestDox('getPrice returns price')]
    public function testGetPriceReturnsPrice()
    {
        $priceListItem = new PriceListItem('name', 'testprice');
        $this->assertEquals('testprice', $priceListItem->getPrice());
    }
}
