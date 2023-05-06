<?php

use Municipio\Helper\TranslationRegistry;

class TranslationRegistryTest extends \PHPUnit\Framework\TestCase
{

    public function testIsDefined()
    {
        $this->assertTrue(class_exists('Municipio\Helper\TranslationRegistry'));
    }

    public function testRegistryIsAnObject()
    {
        $registry = new ReflectionClass('Municipio\Helper\TranslationRegistry');
        $property = $registry->getProperty('collection');
        $this->assertEquals('object', $property->getType()->getName());
    }

    public function testRegistryIsPrivate()
    {
        $registry = new ReflectionClass('Municipio\Helper\TranslationRegistry');
        $property = $registry->getProperty('collection');
        $this->assertTrue($property->isPrivate());
    }

    public function testRegistryIsStatic()
    {
        $registry = new ReflectionClass('Municipio\Helper\TranslationRegistry');
        $property = $registry->getProperty('collection');
        $this->assertTrue($property->isStatic());
    }

    public function getRegistryReturnsRegistry()
    {
        $registry = new TranslationRegistry();
        $this->assertIsObject($registry->getRegistry());
    }

    public function testGetReturnsValueFromRegistryByKey()
    {
        $registry = new TranslationRegistry();
        $registry->add('key', 'value');
        $this->assertEquals('value', $registry->get('key'));
    }

    public function testGetReturnsEmptyStringIfKeyDoesNotExist()
    {
        $registry = new TranslationRegistry();
        $result = null;

        try {
            $result = $registry->get('key');
        } catch (\Throwable $th) {
            $this->assertEquals('', $result);
        }
    }

    public function testGetEmitsNoticeIfKeyDoesNotExist()
    {
        $registry = new TranslationRegistry();

        try {
            $registry->get('key');
        } catch (\Throwable $th) {
            $exceptionClass = get_class($th);
            $this->assertEquals('PHPUnit\Framework\Error\Notice', $exceptionClass);
        }
    }

    public function testAddAcceptsKeyAndValueAsInput()
    {
        $this->expectNotToPerformAssertions();

        $registry = new TranslationRegistry();
        $registry->add('key', 'value');
    }

    public function testAddAddsKeyAndValueToRegistry()
    {
        $registry = new TranslationRegistry();
        $registry->add('key', 'value');
        $this->assertEquals('value', $registry->getCollection()->key);
    }

    public function testUpdateSetsNewValueByKey()
    {
        $registry = new TranslationRegistry();
        $registry->add('key', 'value');
        $registry->update('key', 'newValue');
        $this->assertEquals('newValue', $registry->getCollection()->key);
    }

    public function testUpdateThrowsIfKeyIsNotSet()
    {
        $registry = new TranslationRegistry();

        try {
            $registry->update('key', 'newValue');
        } catch (\Throwable $th) {
            $exceptionClass = get_class($th);
            $this->assertEquals('PHPUnit\Framework\Error\Notice', $exceptionClass);
        }
    }

    public function testUpdateSetsProvidedKeyIfKeyIsNotSet()
    {
        // Given
        $registry = new TranslationRegistry();
        $keyName = 'key';
        $newValue = 'newValue';

        try {
            // When
            $registry->update($keyName, $newValue);
        } catch (\Throwable $th) {
            // Then
            $this->assertEquals($newValue, $registry->getCollection()->$keyName);
        }
    }
}
