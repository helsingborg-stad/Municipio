<?php

use Municipio\Controller\BaseController;
use Municipio\Helper\TranslationRegistry;

class BaseControllerTest extends \PHPUnit\Framework\TestCase {
    
    public function testIsDefined() {
        $this->assertTrue(class_exists('Municipio\Controller\BaseController'));
    }

    public function testHasPropertyLang() {
        $baseController = new ReflectionClass('Municipio\Controller\BaseController');
        $this->assertTrue($baseController->hasProperty('lang'));
    }

    public function testPropertyLangIsPrivate() {
        $baseController = new ReflectionClass('Municipio\Controller\BaseController');
        $property = $baseController->getProperty('lang');
        $this->assertTrue($property->isPrivate());
    }

    public function testPropertyLangIsATranslationRegistry() {
        $baseController = new ReflectionClass('Municipio\Controller\BaseController');
        $property = $baseController->getProperty('lang');
        $this->assertEquals('Municipio\Helper\TranslationRegistry', $property->getType()->getName());
    }

    public function testGetLangCollectionReturnsObject() {
        $baseController = new BaseController();
        $this->assertTrue(is_object($baseController->getLangCollection()));
    }

    public function testLangContainsDefaultValues() {
        $baseController = new BaseController();
        $this->assertTrue(isset($baseController->getLangCollection()->goToHomepage));
    }

    public function testAddTranslationAddsTranslation() {
        $baseController = new BaseController();
        $key = 'key';
        $value = 'value';

        $baseController->addTranslation($key, $value);
        $languageCollection = $baseController->getLangCollection();

        $this->assertEquals($value, $languageCollection->$key);
    }

    public function testUpdateTranslationUpdatesOrAddsTranslation() {
        $baseController = new BaseController();
        $key = 'key';
        $value = 'value';
        $newValue = 'newValue';

        $baseController->updateTranslation($key, $value);
        $languageCollection = $baseController->getLangCollection();
        $this->assertEquals($value, $languageCollection->$key);

        $baseController->updateTranslation($key, $newValue);
        $updatedLanguageCollection = $baseController->getLangCollection();
        $this->assertEquals($newValue, $updatedLanguageCollection->$key);
    }
}