<?php

namespace Municipio\ImageConvert\Logging;

use Municipio\ImageConvert\Logging\Writers\ErrorLogWriter;
use Municipio\ImageConvert\Logging\Writers\LogWriterDatabase;
use Municipio\ImageConvert\Config\ImageConvertConfigInterface;
use Municipio\ImageConvert\Logging\Writers\LogWriterInterface;

class LogWriterFactory
{
  /**
   * Create a log writer instance based on configuration or provided writer.
   *
   * @param ImageConvertConfigInterface $config Configuration object with getDefaultImageConversionLogWriter()
   * @param LogWriterInterface|null $writer Optional custom writer instance
   * @return LogWriterInterface Log writer instance
   */
  public static function create(ImageConvertConfigInterface $config, ?LogWriterInterface $writer = null): LogWriterInterface
  {
    if ($writer !== null) {
      return $writer;
    }

    if ($config->getDefaultImageConversionLogWriter() === 'database') {
      return new LogWriterDatabase();
    }

    return new ErrorLogWriter();
  }
}