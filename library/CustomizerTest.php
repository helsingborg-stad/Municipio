<?php

namespace Municipio;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use wpdb;
use WpService\Implementations\FakeWpService;

function is_admin(): bool
{
    return false;
}

class CustomizerTest extends TestCase
{
    #[TestDox('sanitizeDefaultArrayValue converts empty string value to array if default is array')]
    public function testSanitizeDefaultArrayValueConvertsEmptyStringValueToArrayIfDefaultIsArray()
    {
        $wpService = new FakeWpService([
            'addFilter' => true,
            'addAction' => true,
        ]);
        $wpdb = new wpdb('', '', '', '');

        $value = '';
        $default = ['foo' => 'bar'];
        $customizer = new \Municipio\Customizer(
            $wpService,
            $wpdb,
        );

        $sanitizedValue = $customizer->sanitizeDefaultArrayValue($value, $default);

        $this->assertEquals(['foo' => 'bar'], $sanitizedValue);
    }

    #[TestDox('initApplicators registers direct applicator hooks without using cache wrapper')]
    public function testInitApplicatorsRegistersDirectApplicatorHooksWithoutUsingCacheWrapper(): void
    {
        $wpService = new FakeWpService([
            'addFilter' => true,
            'addAction' => true,
        ]);
        $wpdb = new wpdb('', '', '', '');

        $customizer = new \Municipio\Customizer($wpService, $wpdb);

        $customizer->initApplicators();

        $registeredHooks = array_map(
            fn(array $call): string => $call[0],
            $wpService->methodCalls['addAction'],
        );

        $this->assertContains('wp', $registeredHooks);
        $this->assertContains('rest_api_init', $registeredHooks);
    }
}
