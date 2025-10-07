<?php

namespace Municipio\PostTypeDesign;

use PHPUnit\Framework\TestCase;
use Municipio\PostTypeDesign\ConfigSanitizer;

class ConfigSanitizerTest extends TestCase
{
    public function testTransformReturnsEmptyArrayIfNoConfigOrKeys()
    {
        $instance = new ConfigSanitizer([]);
        $result   = $instance->sanitize();

        $this->assertEmpty($result);
        $this->assertIsArray($result);
    }

    public function testTransformUnsetsConfigKeysNotFoundInKeys()
    {
        $instance = new ConfigSanitizer(['key1' => 'value1', 'key2' => 'value2'], ['key1']);
        $result   = $instance->sanitize();

        $this->assertArrayNotHasKey('key2', $result);
    }

    public function testTransformFillsArrayFromMissingKeys()
    {
        $instance = new ConfigSanitizer(['key1' => 'value1'], ['key1', 'key2']);
        $result   = $instance->sanitize();

        $this->assertArrayHasKey('key2', $result);
    }
}
