<?php

namespace Municipio\ImageConvert;

use PHPUnit\Framework\TestCase;
use Municipio\ImageConvert\Config\ImageConvertConfig;
use WpService\Implementations\FakeWpService;

class ImageConvertFilterTest extends TestCase
{

  /**
   * @testdox Test that the imageDownsize method returns false if the size is not an array.
   * 
   * @dataProvider nonArraySizeProvider
   */
  public function testNonProcessedValues($size): void
  {
      $wpService          = new FakeWpService();
      $configService      = new ImageConvertConfig(
          $wpService
      );
      $imageConvertFilter = new ImageConvertFilter(
          $wpService,
          $configService
      );

      $result = $imageConvertFilter->imageDownsize(false, 1, $size);

      $this->assertEquals(false, $result);
  }

  /**
   * @testdox Test that the imageDownsize method returns a instance of ImageContract if the size is valid.
   * 
   * @dataProvider validSizeProvider
   */
  public function testThatValidSizeReturnsTrue($size): void
  {
    $wpService          = new FakeWpService(
      [
        'applyFilters' => fn($hookName, $value, ...$args) => $value
      ]
    );
    $configService      = new ImageConvertConfig(
        $wpService
    );
    $imageConvertFilter = new ImageConvertFilter(
        $wpService,
        $configService
    );

    $result = $imageConvertFilter->imageDownsize(false, 1, $size);

    $this->assertInstanceOf(
      '\Municipio\ImageConvert\Contract\ImageContract', 
      $result
    );
  }

  /**
   * Test data provider for testNonProcessedValues.
   */
  private function nonArraySizeProvider()
  {
    return [
      ['thumbnail'],      // Not an array
      [123],              // Not an array
      [null],             // Not an array
      [[]],               // Empty array
      [['auto', 'auto']], // No integer values in array
      [['auto', 123]],    // No integer values in array
      [[123, 'auto']],    // No integer values in array
      [[123, null]],      // No null values in array
      [[false, false]]  // No integer values in array
    ];
  }
  
  /**
   * @testdox Test that the imageDownsize method returns false if the size array is not valid.
   * 
   * @dataProvider invalidSizeProvider
   */
  private function validSizeProvider()
  {
    return [
      [[100, 100]], // Valid array
      [[100, false]], // Valid array
      [[false, 100]], // Valid array
    ];
  }
}