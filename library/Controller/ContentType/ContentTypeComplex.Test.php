<?php

namespace Municipio;

use Municipio\Controller\ContentType\ContentTypeComplexInterface;
use Municipio\Helper\ContentType as ContentTypeHelper;

/**
 * Dummy class for testing
 * This class implements ContentTypeComplexInterface for testing purposes
 */
class ContentTypeComplexTestDummy extends \Municipio\Controller\ContentType\ContentTypeFactory implements ContentTypeComplexInterface
{
    protected array $addedContentType = [];

    public function __construct()
    {
        $this->key = 'test';
        $this->label = __('Test', 'municipio');

        parent::__construct($this->key, $this->label);
    }

    /**
     * Adds a secondary content type if it's a simple content type
     *
     * @param ContentTypeComponentInterface $contentType
     * @return void
     */
    public function addSecondaryContentType(ContentTypeComponentInterface $contentType): void
    {
        if (ContentTypeHelper::validateSimpleContentType($contentType, $this)) {
            $this->addedContentType[] = $contentType;
        }
    }

    /**
     * Returns the content types that were successfully added
     *
     * @return array
     */
    public function getAddedContentTypes(): array
    {
        return $this->addedContentType;
    }
}

/**
 * Test class for ContentTypeComplexInterface
 * These tests ensure that any class implementing ContentTypeComplexInterface behaves as expected
 */
class ContentTypeComplexInterfaceTest extends WP_UnitTestCase //phpcs:ignore
{
    public function set_up() // phpcs:ignore
    {
        parent::set_up();
    }

    /**
     * Test that the dummy class is properly defined
     */
    public function testClassIsDefined()
    {
        $this->assertTrue(class_exists(ContentTypeComplexTestDummy::class));
    }

    /**
     * Test that the addSecondaryContentType method correctly adds a simple content type
     */
    public function testAddSecondaryContentTypeAddsSimpleContentType()
    {
        $dummy = new ContentTypeComplexTestDummy();
        $simpleContentType = new \Municipio\Controller\ContentType\Place();  // Assuming Place is a simple content type

        $dummy->addSecondaryContentType($simpleContentType);

        $this->assertContains($simpleContentType, $dummy->getAddedContentTypes());
    }

    /**
     * Test that the addSecondaryContentType method correctly rejects a complex content type
     */
    public function testAddSecondaryContentTypeDoesNotAddComplexContentType()
    {
        $dummy = new ContentTypeComplexTestDummy();
        $complexContentType = new \Municipio\Controller\ContentType\Project();  // Assuming Project is a complex content type

        $dummy->addSecondaryContentType($complexContentType);

        $this->assertNotContains($complexContentType, $dummy->getAddedContentTypes());
    }
}
