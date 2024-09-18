<?php

namespace Municipio\Helper;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class S3
{
    private static $s3Client;

  /**
   * Retrieve the S3 client instance.
   *
   * @return S3Client
   */
    private static function getS3Client()
    {
        if (!isset(self::$s3Client)) {
            self::$s3Client = new S3Client([
            'version'                 => 'latest',
            'region'                  => defined('S3_UPLOADS_REGION') ? S3_UPLOADS_REGION : 'us-east-1',
            'credentials'             => [
            'key'    => defined('S3_UPLOADS_KEY') ? S3_UPLOADS_KEY : '',
            'secret' => defined('S3_UPLOADS_SECRET') ? S3_UPLOADS_SECRET : '',
            ],
            'endpoint'                => defined('S3_UPLOADS_CUSTOM_ENDPOINT') ? S3_UPLOADS_CUSTOM_ENDPOINT : null,
            'debug'                   => defined('S3_UPLOADS_DEBUG') ? S3_UPLOADS_DEBUG : false,
            'use_path_style_endpoint' => true, // Set this to true to use path-style endpoints
            'bucket'                  => defined('S3_UPLOADS_BUCKET') ? S3_UPLOADS_BUCKET : '',
            ]);
        }

        return self::$s3Client;
    }

  /**
   * Check if an object exists on S3.
   *
   * @param string $key S3 object key
   * @return bool
   */
    public static function objectExistsOnS3($s3Key)
    {
        $s3Key = self::sanitizeS3Key($s3Key);
        try {
            $s3 = self::getS3Client();
            $s3->headObject([
            'Bucket' => S3_UPLOADS_BUCKET,
            'Key'    => $s3Key,
            ]);
            return self::restoreS3Key($s3Key);
        } catch (S3Exception $e) {
            self::bail("Error to check for object: " . $e->getMessage());
        }
        return false;
    }

  /**
   * Upload a file to S3 with the specified key.
   *
   * @param string $filePath Local file path
   * @param string $s3Key    S3 object key
   */
    public static function uploadToS3($localFilePath, $s3Key)
    {
        $s3Key = self::sanitizeS3Key($s3Key);

        try {
            $s3 = self::getS3Client();
            $s3->putObject([
            'Bucket'     => S3_UPLOADS_BUCKET,
            'Key'        => $s3Key,
            'SourceFile' => $localFilePath,
            ]);
            return self::restoreS3Key($s3Key);
        } catch (S3Exception $e) {
            self::bail("Error uploading file to S3: " . $e->getMessage());
        }
        return false;
    }

  /**
   * Download a file from S3 to the specified local path.
   *
   * @param string $s3Key       S3 object key
   * @param string $localPath   Local file path
   *
   * @return string|bool        The filepath to the downloaded file or false
   */
    public static function downloadFromS3($s3Key, $localFilePath)
    {
        $s3Key = self::sanitizeS3Key($s3Key);

        try {
            $s3     = self::getS3Client();
            $result = $s3->getObject([
            'Bucket' => S3_UPLOADS_BUCKET,
            'Key'    => $s3Key,
            ]);

            file_put_contents($localFilePath, $result['Body']);
            return $localFilePath;
        } catch (S3Exception $e) {
            self::bail("Error downloading file from S3: " . $e->getMessage());
        }
        return false;
    }

  /**
   * Check if a given path is an S3 path.
   *
   * @param string $path File path
   * @return bool
   */
    public static function isS3Path($path)
    {
        return strpos($path, 's3://') === 0;
    }

  /**
   * Check if we have some sort of s3 support
   *
   * @return bool
   */
    public static function hasS3Support()
    {
        if (!class_exists('\\Aws\\S3\\S3Client')) {
            return false;
        }
        return true;
    }

  /**
   * Log an error message, display it to the user, and exit the script.
   *
   * @param string $errorMessage The error message to log and display.
   * @return void
   */
    public static function bail($errorMessage, $exit = false)
    {
        error_log($errorMessage);
        echo $errorMessage;
        if ($exit == true) {
            exit(1);
        }
    }

  /**
   * Sanitize an S3 key by removing the 's3://[BUCKET_NAME]/' prefix.
   *
   * @param string $key S3 object key
   * @return string The sanitized S3 object key
   */
    public static function sanitizeS3Key($key)
    {
        return str_replace("s3://" . S3_UPLOADS_BUCKET . "/", '', $key);
    }

  /**
   * Restore an S3 key by adding the 's3://[BUCKET_NAME]/' prefix if not already present.
   *
   * @param string $sanitizedKey The sanitized S3 object key
   * @return string The restored S3 object key
   */
    public static function restoreS3Key($sanitizedKey)
    {
      // Check if the input already contains a protocol
        if (strpos($sanitizedKey, '://') === false) {
            // If not, prepend the S3 protocol
            return "s3://" . S3_UPLOADS_BUCKET . "/" . $sanitizedKey;
        }

      // If the input already contains a protocol, return it as is
        return $sanitizedKey;
    }

  /**
   * Restore an S3 key to an HTTPS link.
   *
   * @param string $sanitizedKey The sanitized S3 object key
   * @return string The restored HTTPS link
   */
    public static function restoreS3KeyToHttps($sanitizedKey)
    {
        if (strpos($sanitizedKey, '://') === false) {
            $bucketUrl = defined('S3_UPLOADS_BUCKET_URL') ? rtrim(S3_UPLOADS_BUCKET_URL, '/') : rtrim(S3_UPLOADS_CUSTOM_ENDPOINT, '/');
            return $bucketUrl . "/" . $sanitizedKey;
        }
        return $sanitizedKey;
    }
}
