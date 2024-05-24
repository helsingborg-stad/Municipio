<?php

namespace Municipio\Tests;

use PHPUnit\Framework\TestCase;

class ConfigSanitizerTest extends TestCase
{
    public function testSetKeySetsANewKey()
    {
        $configSanitizerInstance = new \Municipio\PostTypeDesign\ConfigSanitizer(null);
        $configSanitizerInstance->setKey('key');

        $reflectionClass = new \ReflectionClass($configSanitizerInstance);
        $property = $reflectionClass->getProperty('keys');
        $property->setAccessible(true);

        $keys = $property->getValue($configSanitizerInstance);

        $this->assertContains('key', $keys);
    }

    public function testSetKeysMergesKeys()
    {
        $configSanitizerInstance = new \Municipio\PostTypeDesign\ConfigSanitizer(null);
        $configSanitizerInstance->setKeys(['key1', 'key2']);

        $reflectionClass = new \ReflectionClass($configSanitizerInstance);
        $property = $reflectionClass->getProperty('keys');
        $property->setAccessible(true);

        $keys = $property->getValue($configSanitizerInstance);

        $this->assertContains('key1', $keys);
        $this->assertContains('key2', $keys);
    }

    public function testTransformReturnsReplacedKeys() 
    {
        $configSanitizerInstance = new \Municipio\PostTypeDesign\ConfigSanitizer(['key1' => 'value1', 'key2' => 'value2']);
        $configSanitizerInstance->setKeys(['key2', 'key3']);

        $result = $configSanitizerInstance->transform();

        $this->assertArrayHasKey('key2', $result);
        $this->assertArrayHasKey('key3', $result);
        $this->assertArrayNotHasKey('key1', $result);
    }    
    
    public function testTransformReturnsArrayIfNoConfig() 
    {
        $configSanitizerInstance = new \Municipio\PostTypeDesign\ConfigSanitizer(null);

        $result = $configSanitizerInstance->transform();

        $this->assertIsArray($result);
    }

    public function testTransformConfigIfNoKeys() 
    {
        $array = ['key1' => 'value1'];
        $configSanitizerInstance = new \Municipio\PostTypeDesign\ConfigSanitizer($array);

        $result = $configSanitizerInstance->transform();

        $this->assertEquals($result, $array);
    }
}