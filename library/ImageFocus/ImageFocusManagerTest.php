<?php

declare(strict_types=1);

namespace Municipio\ImageFocus;

use Municipio\ImageFocus\Resolvers\FocusPointResolverInterface;
use Municipio\ImageFocus\Storage\FocusPointStorage;
use Municipio\ImageFocus\Storage\FocusPointStorageInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;
use WpService\WpService;

class ImageFocusManagerTest extends TestCase
{
    #[TestDox('calculate() does not throw when metadata contains width and height as strings')]
    public function testDoesNotThrowWhenMetadataContainsWidthAndHeightAsStrings(): void
    {
        define('DOING_AJAX', true);
        $_POST['action'] = 'upload-attachment';
        $imageFocusManager = new ImageFocusManager(static::createWpService(), static::createFocusPointStorage(), static::createFocusPointResolver());
        $imageFocusManager->calculate(123, ['width' => '100', 'height' => '200'], 'create');

        static::assertTrue(true, 'calculate() should not throw an exception when width and height are strings');
    }

    private static function createWpService(): WpService
    {
        return new class extends FakeWpService {
            public function getPostMimeType(int|\WP_Post $post = null): string|false
            {
                return 'image/jpeg';
            }

            public function getAttachedFile(int $attachmentId, bool $unfiltered = false): string|false
            {
                // return this file
                return __DIR__ . '/ImageFocusManagerTest.php';
            }
        };
    }

    private static function createFocusPointStorage(): FocusPointStorageInterface
    {
        return new class implements FocusPointStorageInterface {
            public function get(int $attachmentId): ?array
            {
                return null;
            }

            public function set(int $attachmentId, array $focus): bool
            {
                return true;
            }
        };
    }

    private static function createFocusPointResolver(): FocusPointResolverInterface
    {
        return new class implements FocusPointResolverInterface {
            public function isSupported(): bool
            {
                return true;
            }

            public function resolve(string $filePath, int $width, int $height, ?int $attachmentId = null): ?array
            {
                return ['x' => 0.5, 'y' => 0.5];
            }
        };
    }
}
