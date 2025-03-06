<?php

namespace Municipio\ExternalContent\WpTermFactory;

use PHPUnit\Framework\TestCase;

class WpTermFactoryTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $wpTermFactory = new WpTermFactory();
        $this->assertInstanceOf(WpTermFactory::class, $wpTermFactory);
    }

    /**
     * @testdox create returns a WP_Term object
     */
    public function testCreateReturnsAWpTermObject()
    {
        $wpTermFactory = new WpTermFactory();
        $wpTerm        = $wpTermFactory->create('schemaObject', 'taxonomy');

        $this->assertInstanceOf(\WP_Term::class, $wpTerm);
    }
}
