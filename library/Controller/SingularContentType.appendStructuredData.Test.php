<?php

namespace Municipio\Controller\Test;

use Mockery;
use Municipio\Controller\SingularContentType;
use WP_Mock\Tools\TestCase;

class SingularContentTypeAppendStructuredDataTest extends TestCase
{
    /**
     * @covers SingularContentType::appendStructuredData
     */
    public function testAppendStructuredDataReturnsJsonEncodedArray()
    {
        // Given
        $singularContentType = $this->getSingularContentTypeWithoutConstructor();
        $this->setInaccessibleProperty($singularContentType, get_class($singularContentType), 'contentType', $this->getContentTypeMock('foo'));
        $this->setupDataHelperMock();

        // When
        $result = $singularContentType->appendStructuredData();

        // Then
        $this->assertJson($result);
        $this->assertIsArray(json_decode($result));
        $this->assertCount(1, json_decode($result));
        $this->assertContains('foo', json_decode($result));
    }

    /**
     * @covers SingularContentType::appendStructuredData
     */
    public function testAppendStructuredDataAppendsSecondaryContentTypeIfSet()
    {
        // Given
        $singularContentType = $this->getSingularContentTypeWithoutConstructor();
        $mockedContentType = $this->getContentTypeMock('foo');
        $mockedContentType->secondaryContentType = [$this->getContentTypeMock('bar')];
        $this->setInaccessibleProperty($singularContentType, get_class($singularContentType), 'contentType', $mockedContentType);
        $this->setupDataHelperMock();

        // When
        $result = $singularContentType->appendStructuredData();

        // Then
        $this->assertCount(2, json_decode($result));
        $this->assertContains('foo', json_decode($result));
        $this->assertContains('bar', json_decode($result));
    }

    private function getContentTypeMock($strucuredData)
    {
        return Mockery::mock(\stdClass::class)
            ->shouldReceive('getStructuredData')
            ->andReturn($strucuredData)->getMock();
    }

    private function setupDataHelperMock()
    {
        $this
            ->mockStaticMethod(\Municipio\Helper\Data::class, 'prepareStructuredData')
            ->andReturnUsing('json_encode');
    }

    private function getSingularContentTypeWithoutConstructor()
    {
        return Mockery::mock(SingularContentType::class)->makePartial();
    }
}
