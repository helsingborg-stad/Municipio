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
        $acfJsonFile = __DIR__ . '/../../AcfFields/json/options-theme-features.json';

        $this->assertFileExists($acfJsonFile);
    }

    /**
     * @testdox ACF json file contains options for branded emails
     */
    public function testAcfConfigContainsOptions()
    {
        $jsonFileContents = file_get_contents(__DIR__ . '/../../AcfFields/json/options-theme-features.json');
        $json             = json_decode($jsonFileContents, true);
        $fields           = $json[0]['fields'];
        $fieldNames       = array_map(fn($field) => $field['name'], $fields);

        $config = new BrandedEmailsConfigService($this->getAcfService(([])));

        $this->assertContains($config::OPTION_ENABLED_KEY, $fieldNames);
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
