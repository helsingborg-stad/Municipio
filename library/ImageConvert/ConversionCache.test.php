<?php

namespace Municipio\ImageConvert;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ConversionCacheTest extends TestCase
{
    private ConversionCache $conversionCache;
    private FakeWpService $wpService;

    protected function setUp(): void
    {
        $this->wpService = new FakeWpService([
            'wpCacheGet' => false,
            'wpCacheSet' => true,
            'wpCacheDelete' => true,
            'applyFilters' => function($hook, $value) { return $value; }
        ]);
        $this->conversionCache = new ConversionCache($this->wpService);
    }

    /**
     * @testdox Conversion lock can be acquired and released
     */
    public function testConversionLockAcquireAndRelease(): void
    {
        $imageId = 123;
        $width = 800;
        $height = 600;
        $format = 'webp';

        // Initially should not be locked
        $this->assertFalse($this->conversionCache->isConversionLocked($imageId, $width, $height, $format));

        // Should be able to acquire lock
        $this->assertTrue($this->conversionCache->acquireConversionLock($imageId, $width, $height, $format));

        // Should now be locked
        $this->assertTrue($this->conversionCache->isConversionLocked($imageId, $width, $height, $format));

        // Should be able to release lock
        $this->assertTrue($this->conversionCache->releaseConversionLock($imageId, $width, $height, $format));
    }

    /**
     * @testdox Conversion status can be set and retrieved
     */
    public function testConversionStatusSetAndGet(): void
    {
        $imageId = 123;
        $width = 800;
        $height = 600;
        $format = 'webp';

        // Initially should have no status
        $this->assertNull($this->conversionCache->getConversionStatus($imageId, $width, $height, $format));

        // Set status to pending
        $this->assertTrue($this->conversionCache->setConversionStatus($imageId, $width, $height, $format, ConversionCache::STATUS_PENDING));

        // Should retrieve pending status
        $this->assertEquals(ConversionCache::STATUS_PENDING, $this->conversionCache->getConversionStatus($imageId, $width, $height, $format));
    }

    /**
     * @testdox Recent failure check works correctly
     */
    public function testHasRecentFailure(): void
    {
        $imageId = 123;
        $width = 800;
        $height = 600;
        $format = 'webp';

        // Initially should not have recent failure
        $this->assertFalse($this->conversionCache->hasRecentFailure($imageId, $width, $height, $format));

        // Mark as failed
        $this->assertTrue($this->conversionCache->markConversionFailed($imageId, $width, $height, $format));

        // Should now have recent failure
        $this->assertTrue($this->conversionCache->hasRecentFailure($imageId, $width, $height, $format));
    }

    /**
     * @testdox Success marking works correctly
     */
    public function testMarkConversionSuccess(): void
    {
        $imageId = 123;
        $width = 800;
        $height = 600;
        $format = 'webp';

        // Mark as successful
        $this->assertTrue($this->conversionCache->markConversionSuccess($imageId, $width, $height, $format));

        // Should have success status
        $this->assertEquals(ConversionCache::STATUS_SUCCESS, $this->conversionCache->getConversionStatus($imageId, $width, $height, $format));

        // Should not have recent failure
        $this->assertFalse($this->conversionCache->hasRecentFailure($imageId, $width, $height, $format));
    }

    /**
     * @testdox Background queue functionality works
     */
    public function testQueueForBackgroundConversion(): void
    {
        $imageId = 123;
        $width = 800;
        $height = 600;
        $format = 'webp';
        $conversionData = ['quality' => 80];

        // Should be able to queue for background conversion
        $this->assertTrue($this->conversionCache->queueForBackgroundConversion($imageId, $width, $height, $format, $conversionData));

        // Should be marked as queued
        $this->assertTrue($this->conversionCache->isQueuedForConversion($imageId, $width, $height, $format));

        // Queued conversions should now return the queued item
        $queued = $this->conversionCache->getQueuedConversions();
        $this->assertIsArray($queued);
        $this->assertCount(1, $queued);
        
        $queuedItem = $queued[0];
        $this->assertEquals($imageId, $queuedItem['image_id']);
        $this->assertEquals($width, $queuedItem['width']);
        $this->assertEquals($height, $queuedItem['height']);
        $this->assertEquals($format, $queuedItem['format']);
        $this->assertEquals($conversionData, $queuedItem['data']);
        
        // Should be able to remove from queue
        $this->assertTrue($this->conversionCache->removeFromQueue($imageId, $width, $height, $format));
        
        // Should no longer be queued
        $this->assertFalse($this->conversionCache->isQueuedForConversion($imageId, $width, $height, $format));
        
        // Queue should be empty again
        $queued = $this->conversionCache->getQueuedConversions();
        $this->assertCount(0, $queued);
    }

    /**
     * @testdox Cache clearing works
     */
    public function testClearImageCache(): void
    {
        $imageId = 123;

        // Should be able to clear cache
        $this->assertTrue($this->conversionCache->clearImageCache($imageId));
    }

    /**
     * @testdox Different image dimensions create different cache keys
     */
    public function testDifferentDimensionsUseDifferentCacheKeys(): void
    {
        $imageId = 123;
        $format = 'webp';

        // Set status for first dimension
        $this->assertTrue($this->conversionCache->setConversionStatus($imageId, 800, 600, $format, ConversionCache::STATUS_SUCCESS));

        // Set status for second dimension
        $this->assertTrue($this->conversionCache->setConversionStatus($imageId, 400, 300, $format, ConversionCache::STATUS_FAILED));

        // Should have different statuses
        $this->assertEquals(ConversionCache::STATUS_SUCCESS, $this->conversionCache->getConversionStatus($imageId, 800, 600, $format));
        $this->assertEquals(ConversionCache::STATUS_FAILED, $this->conversionCache->getConversionStatus($imageId, 400, 300, $format));
    }

    /**
     * @testdox Queue limit functionality works correctly
     */
    public function testQueueLimitFunctionality(): void
    {
        // Queue multiple items
        for ($i = 1; $i <= 15; $i++) {
            $this->assertTrue($this->conversionCache->queueForBackgroundConversion($i, 800, 600, 'webp'));
        }
        
        // Get limited number of queued conversions
        $queued = $this->conversionCache->getQueuedConversions(5);
        $this->assertCount(5, $queued);
        
        // Get all queued conversions
        $allQueued = $this->conversionCache->getQueuedConversions(20);
        $this->assertCount(15, $allQueued);
        
        // Verify correct order (should be in queue order)
        $this->assertEquals(1, $allQueued[0]['image_id']);
        $this->assertEquals(2, $allQueued[1]['image_id']);
    }

    /**
     * @testdox Different formats create different cache keys
     */
    public function testDifferentFormatsUseDifferentCacheKeys(): void
    {
        $imageId = 123;
        $width = 800;
        $height = 600;

        // Set status for webp format
        $this->assertTrue($this->conversionCache->setConversionStatus($imageId, $width, $height, 'webp', ConversionCache::STATUS_SUCCESS));

        // Set status for jpeg format
        $this->assertTrue($this->conversionCache->setConversionStatus($imageId, $width, $height, 'jpeg', ConversionCache::STATUS_FAILED));

        // Should have different statuses
        $this->assertEquals(ConversionCache::STATUS_SUCCESS, $this->conversionCache->getConversionStatus($imageId, $width, $height, 'webp'));
        $this->assertEquals(ConversionCache::STATUS_FAILED, $this->conversionCache->getConversionStatus($imageId, $width, $height, 'jpeg'));
    }
}