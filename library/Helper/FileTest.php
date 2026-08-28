<?php

namespace Municipio\Helper;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class FileTest extends TestCase
{
    private array $temporaryFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->temporaryFiles as $temporaryFile) {
            if (file_exists($temporaryFile)) {
                unlink($temporaryFile);
            }
        }

        parent::tearDown();
    }

    #[TestDox('fileExists should return true for an existing file')]
    public function testFileExistsReturnsTrueForExistingFile()
    {
        $filePath = __FILE__; // This file.
        $wpService = new FakeWpService(['wpCacheGet' => false, 'wpCacheSet' => true]);
        WpService::set($wpService);
        $this->assertTrue(File::fileExists($filePath));
    }

    #[TestDox('fileExists should return false for a non-existing file')]
    public function testFileExistsReturnsFalseForNonExistingFile()
    {
        $filePath = __DIR__ . '/non_existing_file.txt'; // A file that does not exist.
        $wpService = new FakeWpService(['wpCacheGet' => false, 'wpCacheSet' => true]);
        WpService::set($wpService);
        $this->assertFalse(File::fileExists($filePath));
    }

    #[TestDox('fileExists should cache the result for found files')]
    public function testFileExistsCachesFoundFiles()
    {
        $filePath = __FILE__; // This file.
        $wpService = new FakeWpService(['wpCacheGet' => false, 'wpCacheSet' => true]);
        WpService::set($wpService);

        // Clear runtime cache before test
        File::clearRuntimeCache();

        // First call should not be cached
        $this->assertTrue(File::fileExists($filePath));

        // Now it should be cached
        $this->assertEquals('found', $wpService->methodCalls['wpCacheSet'][0][1]);
    }

    #[TestDox('fileExists should cache the result for non-existing files')]
    public function testFileExistsCachesNonExistingFiles()
    {
        $filePath = __DIR__ . '/non_existing_file.txt'; // A file that does not exist.
        $wpService = new FakeWpService(['wpCacheGet' => false, 'wpCacheSet' => true]);
        WpService::set($wpService);

        // Clear runtime cache before test
        File::clearRuntimeCache();

        // First call should not be cached
        $this->assertFalse(File::fileExists($filePath));

        // Now it should be cached
        $this->assertEquals('not_found', $wpService->methodCalls['wpCacheSet'][0][1]);
    }

    #[TestDox('fileExists should use runtime cache for repeated calls within same request')]
    public function testFileExistsUsesRuntimeCache()
    {
        $filePath = __FILE__; // This file.
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

    #[TestDox('isValidImage should reject empty and malformed files')]
    public function testIsValidImageRejectsInvalidFiles(): void
    {
        $emptyFile = $this->createTemporaryFile('');
        $malformedFile = $this->createTemporaryFile('not an image');

        $this->assertFalse(File::isValidImage($emptyFile));
        $this->assertFalse(File::isValidImage($malformedFile));
    }

    #[TestDox('isValidImage should validate image type and dimensions')]
    public function testIsValidImageValidatesExpectedProperties(): void
    {
        $imageFile = $this->createTemporaryFile(
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=', true),
        );

        $this->assertTrue(File::isValidImage($imageFile, 'image/png', 1, 1));
        $this->assertFalse(File::isValidImage($imageFile, 'image/webp', 1, 1));
        $this->assertFalse(File::isValidImage($imageFile, 'image/png', 2, 1));
        $this->assertFalse(File::isValidImage($imageFile, 'image/png', 1, 2));
    }

    #[TestDox('getImageMimeTypeForPath should resolve supported file extensions')]
    public function testGetImageMimeTypeForPathResolvesSupportedExtensions(): void
    {
        $this->assertSame('image/jpeg', File::getImageMimeTypeForPath('/uploads/image.JPG'));
        $this->assertSame('image/webp', File::getImageMimeTypeForPath('/uploads/image.webp'));
        $this->assertNull(File::getImageMimeTypeForPath('/uploads/image.unknown'));
    }

    private function createTemporaryFile(string $contents): string
    {
        $temporaryFile = tempnam(sys_get_temp_dir(), 'municipio-image-test-');
        if ($temporaryFile === false) {
            $this->fail('Could not create a temporary file.');
        }

        file_put_contents($temporaryFile, $contents);
        $this->temporaryFiles[] = $temporaryFile;

        return $temporaryFile;
    }
}
