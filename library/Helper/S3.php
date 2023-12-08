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
                'version' => 'latest',
                'region' => defined('S3_UPLOADS_REGION') ? S3_UPLOADS_REGION : 'us-east-1',
                'credentials' => [
                    'key' => defined('S3_UPLOADS_KEY') ? S3_UPLOADS_KEY : '',
                    'secret' => defined('S3_UPLOADS_SECRET') ? S3_UPLOADS_SECRET : '',
                ],
                'endpoint' => defined('S3_UPLOADS_CUSTOM_ENDPOINT') ? S3_UPLOADS_CUSTOM_ENDPOINT : null,
                'debug' => defined('S3_UPLOADS_DEBUG') ? S3_UPLOADS_DEBUG : false,
                'use_path_style_endpoint' => true, // Set this to true to use path-style endpoints
                'bucket' => defined('S3_UPLOADS_BUCKET') ? S3_UPLOADS_BUCKET : '',
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
    public static function objectExistsOnS3($key)
    {
        try {
            $s3 = self::getS3Client();
            $s3->headObject([
                'Bucket' => S3_UPLOADS_BUCKET,
                'Key' => $key,
            ]);

            return true;
        } catch (S3Exception $e) {
            return false;
        }
    }

    /**
     * Upload a file to S3 with the specified key.
     *
     * @param string $filePath Local file path
     * @param string $s3Key    S3 object key
     */
    public static function uploadToS3($filePath, $s3Key)
    {
        try {
            $s3 = self::getS3Client();
            $s3->putObject([
                'Bucket' => S3_UPLOADS_BUCKET,
                'Key' => $s3Key,
                'SourceFile' => $filePath,
            ]);
        } catch (S3Exception $e) {
            self::bail("Error uploading file to S3: " . $e->getMessage());
        }
    }

    /**
     * Download a file from S3 to the specified local path.
     *
     * @param string $s3Key       S3 object key
     * @param string $localPath   Local file path
     */
    public static function downloadFromS3($s3Key, $localPath)
    {
        try {
            $s3 = self::getS3Client();
            $result = $s3->getObject([
                'Bucket' => S3_UPLOADS_BUCKET,
                'Key' => $s3Key,
            ]);

            file_put_contents($localPath, $result['Body']);
        } catch (S3Exception $e) {
            self::bail("Error downloading file from S3: " . $e->getMessage());
        }
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
    public function hasS3Support() {
      if (!class_exists('\\Aws\\S3\\S3Client')) {
        return false;
      }
      return true;
    }
}