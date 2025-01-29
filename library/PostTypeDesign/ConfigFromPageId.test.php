<?php

namespace Municipio\PostTypeDesign;

use PHPUnit\Framework\TestCase;
use Municipio\PostTypeDesign\ConfigFromPageId;
use WpService\Contracts\IsWpError;
use WpService\Contracts\WpRemoteRetrieveBody;
use WP_Error;
use WpService\Contracts\WpRemoteGet;

class ConfigFromPageIdTest extends TestCase
{
    public function testGetReturnsDefault()
    {
        $wpService = $this->getWpService();
        $instance  = new ConfigFromPageId($wpService);

        $result = $instance->get('123');

        $this->assertEmpty($result[0]);
        $this->assertIsArray($result[0]);
        $this->assertNull($result[1]);
    }

    public function testGetReturnsArrayWithValues()
    {
        $wpService = $this->getWpService([
            'wpRemoteRetrieveBody' => '{"mods": {"test": "test"}, "css": "test"}'
        ]);
        $instance  = new ConfigFromPageId($wpService);

        $result = $instance->get('123');

        $this->assertArrayHasKey('test', $result[0]);
        $this->assertEquals('test', $result[1]);
    }

    public function testGetReturnsDefaultIfWpError()
    {
        $wpService = $this->getWpService([
            'isWPError' => true
        ]);

        $instance = new ConfigFromPageId($wpService);

        $result = $instance->get('123');

        $this->assertEmpty($result[0]);
        $this->assertIsArray($result[0]);
        $this->assertNull($result[1]);
    }

    private function getWpService(array $db = [])
    {
        return new class ($db) implements IsWpError, WpRemoteGet, WpRemoteRetrieveBody {
            public function __construct(private array $db)
            {
            }

            public function isWPError(mixed $thing): bool
            {
                return $this->db['isWPError'] ?? false;
            }

            public function wpRemoteGet(string $url, array $args = []): array|WP_Error
            {
                return $this->db['wpRemoteGet'] ?? [];
            }

            public function wpRemoteRetrieveBody(array|WP_Error $response): string
            {
                return $this->db['wpRemoteRetrieveBody'] ?? "";
            }
        };
    }
}
