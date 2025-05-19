<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler;

use Municipio\Schema\Schema;
use PHPUnit\Framework\TestCase;

class EmailHandlerTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated(): void
    {
        $emailHandler = new EmailHandler();
        $this->assertInstanceOf(EmailHandler::class, $emailHandler);
    }

    /**
     * @testdox supports method returns true for valid email and email field type
     */
    public function testSupportsMethodReturnsTrueForValidEmail(): void
    {
        $emailHandler = new EmailHandler();
        $propertyName = 'email';
        $fieldType    = 'email';
        $value        = 'test@example.com';

        $this->assertTrue($emailHandler->supports($propertyName, $fieldType, $value, ['string']));
    }

    /**
     * @testdox supports method returns false for invalid email
     */
    public function testSupportsMethodReturnsFalseForInvalidEmail(): void
    {
        $emailHandler = new EmailHandler();
        $propertyName = 'email';
        $fieldType    = 'email';
        $value        = 'invalid-email';

        $this->assertFalse($emailHandler->supports($propertyName, $fieldType, $value, ['string']));
    }

    /**
     * @testdox supports method returns false for non-email field type
     */
    public function testSupportsMethodReturnsFalseForNonEmailFieldType(): void
    {
        $emailHandler = new EmailHandler();
        $propertyName = 'email';
        $fieldType    = 'text';
        $value        = 'test@example.com';

        $this->assertFalse($emailHandler->supports($propertyName, $fieldType, $value, ['string']));
    }

    /**
     * @testdox handle method sets the email property on the schema object
     */
    public function testHandleMethodSetsEmailProperty(): void
    {
        $emailHandler = new EmailHandler();
        $schemaObject = Schema::person();
        $propertyName = 'email';
        $value        = 'test@example.com';

        $emailHandler->handle($schemaObject, $propertyName, $value);

        $this->assertEquals($value, $schemaObject->getProperty($propertyName));
    }
}
