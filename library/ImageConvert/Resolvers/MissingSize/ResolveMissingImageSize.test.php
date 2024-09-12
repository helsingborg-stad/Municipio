<?php

namespace Municipio\ImageConvert;

use PHPUnit\Framework\TestCase;
use Municipio\ImageConvert\Config\ImageConvertConfig;
use WpService\Implementations\FakeWpService;
use Municipio\ImageConvert\Resolvers\MissingSize\ResolveMissingImageSize;
use Municipio\ImageConvert\Contract\ImageContract;

class ResolveMissingImageSizeTest extends TestCase
{

  /**
   * @testdox Test that calculateScaledDimensions returns the correct scaled dimensions.
   * 
   * @dataProvider scaleProvider
   */
  public function testScalerReturnsCorrectlyScaledValue($requestedSize, $resolvedSize, $expected): void
  {
      $wpService          = new FakeWpService();
      $configService      = new ImageConvertConfig(
          $wpService
      );
      
      $imageContract = ImageContract::factory($wpService, 1, $requestedSize[0], $requestedSize[1]);

      $result = (new ResolveMissingImageSize(
        $wpService,
        $configService
      ))->calculateScaledDimensions($imageContract, $resolvedSize);

      $this->assertEquals($expected, $result);
  }
  
  /**
   * @testdox Test that the imageDownsize method returns false if the size array is not valid.
   * 
   * @dataProvider invalidSizeProvider
   */
  private function scaleProvider()
  {
    return [
      [[100, false], [50, 50], ['width' => 100, 'height' => 100]],   //Resolved size is less than input size
      [[100, false], [200, 200], ['width' => 100, 'height' => 100]], //Resolved size is more than input size
      [[100, false], [100, 100], ['width' => 100, 'height' => 100]], //Resolved size is equal to input size
      [[false, 100], [100, 100], ['width' => 100, 'height' => 100]], //Width can be resolved
      [[100, false], [100, 100], ['width' => 100, 'height' => 100]], //Height can be resolved
      [[1920, false], [16, 9], ['width' => 1920, 'height' => 1080]], //Correctly scales to 16:9
      [[false, 1080], [16, 9], ['width' => 1920, 'height' => 1080]], //Correctly scales to 16:9
      [[false, 1920], [9, 16], ['width' => 1080, 'height' => 1920]], //Correctly scales to 9:16
      [[1080, false], [9, 16], ['width' => 1080, 'height' => 1920]], //Correctly scales to 9:16
    ];
  }
}