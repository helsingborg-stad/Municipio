<?php

namespace Municipio\Helper;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class FileTest extends TestCase
{
    /**
     * @testdox fileExists should return true for an existing file
     */
    public function testFileExistsReturnsTrueForExistingFile()
    {
        $filePath  = __FILE__; // This file.
        $wpService = new FakeWpService(['wpCacheGet' => false, 'wpCacheSet' => true]);
        WpService::set($wpService);
        $this->assertTrue(File::fileExists($filePath));
    }

    /**
     * @testdox fileExists should return false for a non-existing file
     */
    public function testFileExistsReturnsFalseForNonExistingFile()
    {
        $filePath  = __DIR__ . '/non_existing_file.txt'; // A file that does not exist.
        $wpService = new FakeWpService(['wpCacheGet' => false, 'wpCacheSet' => true]);
        WpService::set($wpService);
        $this->assertFalse(File::fileExists($filePath));
    }

    /**
     * @testdox fileExists should cache the result for found files
     */
    public function testFileExistsCachesFoundFiles()
    {
        $filePath  = __FILE__; // This file.
        $wpService = new FakeWpService(['wpCacheGet' => false, 'wpCacheSet' => true]);
        WpService::set($wpService);

        // Clear runtime cache before test
        File::clearRuntimeCache();

        // First call should not be cached
        $this->assertTrue(File::fileExists($filePath));

        // Now it should be cached
        $this->assertEquals('found', $wpService->methodCalls['wpCacheSet'][0][1]);
    }

    /**
     * @testdox fileExists should cache the result for non-existing files
     */
    public function testFileExistsCachesNonExistingFiles()
    {
        $filePath  = __DIR__ . '/non_existing_file.txt'; // A file that does not exist.
        $wpService = new FakeWpService(['wpCacheGet' => false, 'wpCacheSet' => true]);
        WpService::set($wpService);

        // Clear runtime cache before test
        File::clearRuntimeCache();

        // First call should not be cached
        $this->assertFalse(File::fileExists($filePath));

        // Now it should be cached
        $this->assertEquals('not_found', $wpService->methodCalls['wpCacheSet'][0][1]);
    }

    /**
     * @testdox fileExists should use runtime cache for repeated calls within same request
     */
    public function testFileExistsUsesRuntimeCache()
    {
        $filePath  = __FILE__; // This file.
        $wpService = new FakeWpService(['wpCacheGet' => false, 'wpCacheSet' => true]);
        WpService::set($wpService);

        // Clear runtime cache before test
        File::clearRuntimeCache();

        // First call should check file system and cache
        $this->assertTrue(File::fileExists($filePath));
        $firstCallCount = count($wpService->methodCalls['wpCacheGet'] ?? []);

        // Second call should use runtime cache without additional cache lookups
        $this->assertTrue(File::fileExists($filePath));
        $secondCallCount = count($wpService->methodCalls['wpCacheGet'] ?? []);

        // Should have same number of cache get calls (runtime cache prevented additional lookups)
        $this->assertEquals($firstCallCount, $secondCallCount);
    }
}
