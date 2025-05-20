<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

use PHPUnit\Framework\TestCase;

class EmailFieldTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $emailField = new EmailField('email', 'Email', 'email');
        $this->assertInstanceOf(EmailField::class, $emailField);
    }

    /**
     * @testdox getValue() returns sanitized email
     */
    public function testGetValueReturnsSanitizedEmail()
    {
        $emailField = new EmailField('email', 'Email', 'test@example.com');
        $this->assertEquals('test@example.com', $emailField->getValue());
    }

    /**
     * @testdox getValue() returns empty string for invalid email
     */
    public function testGetValueReturnsEmptyStringForInvalidEmail()
    {
        $emailField = new EmailField('email', 'Email', 'invalid-email');
        $this->assertEquals('', $emailField->getValue());
    }
}
