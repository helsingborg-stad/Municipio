<?php

namespace Municipio\BrandedEmails\Config;

use AcfService\Contracts\GetField;
use PHPUnit\Framework\TestCase;

class BrandedEmailsConfigServiceTest extends TestCase
{
    /**
     * @testdox ACF json file exists
     */
    public function testAcfConfig()
    {
        $acfJsonFile = __DIR__ . '/../../AcfFields/json/options-branded-emails.json';

        $this->assertFileExists($acfJsonFile);
    }

    /**
     * @testdox ACF json file contains options for branded emails
     */
    public function testAcfConfigContainsOptions()
    {
        $jsonFileContents = file_get_contents(__DIR__ . '/../../AcfFields/json/options-branded-emails.json');
        $json             = json_decode($jsonFileContents, true);
        $fields           = $json[0]['fields'];
        $fieldNames       = array_map(fn($field) => $field['name'], $fields);

        $config = new BrandedEmailsConfigService($this->getAcfService(([])));

        $this->assertContains($config::OPTION_ENABLED_KEY, $fieldNames);
        $this->assertContains($config::OPTION_MAIL_FROM_KEY, $fieldNames);
        $this->assertContains($config::OPTION_MAIL_FROM_NAME_KEY, $fieldNames);
    }

    /**
     * @testdox isEnabled() returns true if value is truthy
     * @covers \Municipio\BrandedEmails\Config\BrandedEmailsConfigService::isEnabled
     * @testWith [true]
     *           ["1"]
     *           [1]
     */
    public function testIsEnabledReturnsTrueIfValueIsTruthy($getFieldValue)
    {

        $acfService = $this->getAcfService(['getField' => $getFieldValue]);
        $config     = new BrandedEmailsConfigService($acfService);

        $this->assertTrue($config->isEnabled());
    }

    /**
     * @testdox isEnabled() returns false if value is falsy
     * @covers \Municipio\BrandedEmails\Config\BrandedEmailsConfigService::isEnabled
     * @testWith [false]
     *           [""]
     *           [0]
     */
    public function testIsEnabledReturnsFalseIfValueIsFalsy($getFieldValue)
    {

        $acfService = $this->getAcfService(['getField' => $getFieldValue]);
        $config     = new BrandedEmailsConfigService($acfService);

        $this->assertFalse($config->isEnabled());
    }

    /**
     * @testdox getMailFrom() returns value if value is set
     * @covers \Municipio\BrandedEmails\Config\BrandedEmailsConfigService::getMailFrom
     */
    public function testGetMailFromReturnsValueIfValueIsSet()
    {
        $acfService = $this->getAcfService(['getField' => 'foo@bar.baz']);
        $config     = new BrandedEmailsConfigService($acfService);

        $this->assertEquals('foo@bar.baz', $config->getMailFrom());
    }


    /**
     * @testdox getMailFrom() returns null if value is not set or is not an email
     * @covers \Municipio\BrandedEmails\Config\BrandedEmailsConfigService::getMailFrom
     * @testWith [false]
     *           [""]
     *           [null]
     *           ["foo@bar"]
     */
    public function testGetMailFromReturnsNull($value)
    {
        $acfService = $this->getAcfService(['getField' => $value]);
        $config     = new BrandedEmailsConfigService($acfService);

        $this->assertNull($config->getMailFrom());
    }

    /**
     * @testdox getMailFromName() returns value if value is set
     */
    public function testGetMailFromNameReturnsValueIfValueIsSet()
    {
        $acfService = $this->getAcfService(['getField' => 'foo']);
        $config     = new BrandedEmailsConfigService($acfService);

        $this->assertEquals('foo', $config->getMailFromName());
    }

    /**
     * @testdox getMailFromName() returns null if value is not set
     * @testWith [false]
     *           [""]
     *           [null]
     */
    public function testGetMailFromNameReturnsNullIfValueIsNotSet($value)
    {
        $acfService = $this->getAcfService(['getField' => $value]);
        $config     = new BrandedEmailsConfigService($acfService);

        $this->assertNull($config->getMailFromName());
    }

    private function getAcfService(array $data): GetField
    {
        return new class ($data) implements GetField {
            public function __construct(private array $data)
            {
            }

            public function getField(string $selector, int|false|string $postId = false, bool $formatValue = true, bool $escapeHtml = false)
            {
                return $this->data['getField'];
            }
        };
    }
}
