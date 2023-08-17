<?php

class BaseControllerTest extends \WP_UnitTestCase
{
    public function testGetLogotypeReturnsString()
    {
        $baseController = new \Municipio\Controller\BaseController();
        $this->assertIsString($baseController->getLogotype());
    }

    public function testGetLogotypeReturnsDefaultUrlIfNoOtherFound()
    {
        $baseController = new \Municipio\Controller\BaseController();
        $defaultLogotype = $baseController->getDefaultLogotype();
        $this->assertEquals($defaultLogotype, $baseController->getLogotype());
    }
    
    public function testGetLogotypeReturnsVariantUrlIfSet()
    {
        set_theme_mod('logotype_negative', 'bar');
        $baseController = new \Municipio\Controller\BaseController();
        $this->assertEquals('bar', $baseController->getLogotype('negative'));
    }
    
    public function testGetLogotypeDefaultsToStandard()
    {
        set_theme_mod('logotype', 'foo');
        $baseController = new \Municipio\Controller\BaseController();
        $this->assertEquals('foo', $baseController->getLogotype());
    }
    
    public function testGetLogotypeReturnsEmptyIfNotAvailable()
    {
        $baseController = new \Municipio\Controller\BaseController();
        $this->assertEquals('', $baseController->getLogotype('foo'));
    }
}
