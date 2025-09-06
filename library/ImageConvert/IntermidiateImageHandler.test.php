<?php

namespace Municipio\ImageConvert;

use PHPUnit\Framework\TestCase;
use Municipio\ImageConvert\Config\ImageConvertConfig;
use Municipio\ImageConvert\Contract\ImageContract;
use WpService\Implementations\FakeWpService;

class IntermidiateImageHandlerTest extends TestCase
{
    private IntermidiateImageHandler $handler;
    private FakeWpService $wpService;
    private ImageConvertConfig $config;

    protected function setUp(): void
    {
        $this->wpService = new FakeWpService([
            'wpCacheGet' => false,
            'wpCacheSet' => true,
            'wpCacheDelete' => true,
            'isAdmin' => false,
            'addFilter' => true,
            'isWpError' => false,
            'wpGetImageEditor' => $this->createMockImageEditor(),
            'wpGetAttachmentMetadata' => 1024000, // 1MB
            'wpAttachmentIs' => true
        ]);
        
        $this->config = new ImageConvertConfig($this->wpService);
        $this->handler = new IntermidiateImageHandler($this->wpService, $this->config);
    }

    private function createMockImageEditor()
    {
        return new class {
            public function get_size() {
                return ['width' => 1920, 'height' => 1080];
            }
            
            public function resize($width, $height, $crop) {
                return true;
            }
            
            public function save($path) {
                return ['path' => $path, 'file' => basename($path)];
            }
        };
    }

    private function createMockImageContract(int $id = 123, int $width = 800, int $height = 600): ImageContract
    {
        $mockContract = $this->createMock(ImageContract::class);
        $mockContract->method('getId')->willReturn($id);
        $mockContract->method('getWidth')->willReturn($width);
        $mockContract->method('getHeight')->willReturn($height);
        $mockContract->method('getPath')->willReturn('/path/to/image.jpg');
        $mockContract->method('getUrl')->willReturn('http://example.com/image.jpg');
        $mockContract->method('getIntermidiateLocation')->willReturn([
            'path' => '/path/to/image-800x600.webp',
            'url' => 'http://example.com/image-800x600.webp'
        ]);
        
        return $mockContract;
    }

    /**
     * @testdox createIntermidiateImage should skip conversion for recent failures
     */
    public function testSkipsConversionForRecentFailures()
    {
        $imageContract = $this->createMockImageContract();
        
        // Mock the conversion cache to return recent failure
        $conversionCache = $this->createMock(ConversionCache::class);
        $conversionCache->method('hasRecentFailure')->willReturn(true);
        
        // Use reflection to inject the mock conversion cache
        $reflection = new \ReflectionClass($this->handler);
        $property = $reflection->getProperty('conversionCache');
        $property->setAccessible(true);
        $property->setValue($this->handler, $conversionCache);
        
        $result = $this->handler->createIntermidiateImage($imageContract);
        
        // Should return original image without attempting conversion
        $this->assertSame($imageContract, $result);
    }

    /**
     * @testdox createIntermidiateImage should return existing file if found
     */
    public function testReturnsExistingFileIfFound()
    {
        // Mock File::fileExists to return true
        $this->wpService->methodReturnValues['wpCacheGet'] = 'found';
        
        $imageContract = $this->createMockImageContract();
        $imageContract->expects($this->once())->method('setUrl');
        $imageContract->expects($this->once())->method('setPath');
        
        $result = $this->handler->createIntermidiateImage($imageContract);
        
        $this->assertSame($imageContract, $result);
    }

    /**
     * @testdox createIntermidiateImage should return original if conversion is locked
     */
    public function testReturnsOriginalIfConversionIsLocked()
    {
        $imageContract = $this->createMockImageContract();
        
        // Mock the conversion cache to return locked status
        $conversionCache = $this->createMock(ConversionCache::class);
        $conversionCache->method('hasRecentFailure')->willReturn(false);
        $conversionCache->method('isConversionLocked')->willReturn(true);
        $conversionCache->method('queueForBackgroundConversion')->willReturn(true);
        
        // Use reflection to inject the mock conversion cache
        $reflection = new \ReflectionClass($this->handler);
        $property = $reflection->getProperty('conversionCache');
        $property->setAccessible(true);
        $property->setValue($this->handler, $conversionCache);
        
        $result = $this->handler->createIntermidiateImage($imageContract);
        
        // Should return original image and queue for background processing
        $this->assertSame($imageContract, $result);
    }

    /**
     * @testdox clearAttachmentCache should clear conversion cache for deleted attachments
     */
    public function testClearAttachmentCache()
    {
        $attachmentId = 123;
        
        // Mock the conversion cache
        $conversionCache = $this->createMock(ConversionCache::class);
        $conversionCache->expects($this->once())
                       ->method('clearImageCache')
                       ->with($attachmentId);
        
        // Use reflection to inject the mock conversion cache
        $reflection = new \ReflectionClass($this->handler);
        $property = $reflection->getProperty('conversionCache');
        $property->setAccessible(true);
        $property->setValue($this->handler, $conversionCache);
        
        $this->handler->clearAttachmentCache($attachmentId);
    }

    /**
     * @testdox clearAttachmentCacheOnUpdate should clear cache and return data
     */
    public function testClearAttachmentCacheOnUpdate()
    {
        $attachmentId = 123;
        $data = ['width' => 800, 'height' => 600];
        
        // Mock the conversion cache
        $conversionCache = $this->createMock(ConversionCache::class);
        $conversionCache->expects($this->once())
                       ->method('clearImageCache')
                       ->with($attachmentId);
        
        // Use reflection to inject the mock conversion cache
        $reflection = new \ReflectionClass($this->handler);
        $property = $reflection->getProperty('conversionCache');
        $property->setAccessible(true);
        $property->setValue($this->handler, $conversionCache);
        
        $result = $this->handler->clearAttachmentCacheOnUpdate($data, $attachmentId);
        
        $this->assertSame($data, $result);
    }

    /**
     * @testdox Non-ImageContract instances should be returned unchanged
     */
    public function testNonImageContractReturnedUnchanged()
    {
        $nonImageContract = 'not an image contract';
        
        $result = $this->handler->createIntermidiateImage($nonImageContract);
        
        $this->assertSame($nonImageContract, $result);
    }

    /**
     * @testdox Should fallback gracefully for invalid intermediate location
     */
    public function testFallbackForInvalidIntermediateLocation()
    {
        $imageContract = $this->createMockImageContract();
        $imageContract->method('getIntermidiateLocation')->willReturn([
            'path' => '', // Empty path should trigger fallback
            'url' => ''
        ]);
        
        $result = $this->handler->createIntermidiateImage($imageContract);
        
        $this->assertSame($imageContract, $result);
    }
}