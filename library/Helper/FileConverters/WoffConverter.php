<?php

namespace Municipio\Helper\FileConverters;

use Municipio\Helper\S3 as S3Helper;

class WoffConverter implements FileConverterInterface
{
     /**
     * Converts a WOFF font file to TTF format.
     *
     * This function takes a font ID, retrieves the WOFF font file path, and performs the conversion
     * to TTF format. If S3 support is available and the file is on S3, it handles download,
     * conversion, and upload operations. If the file is local, it directly converts it to TTF.
     *
     * @param int $fontId The font ID to convert.
     * @return string The path or S3 key of the converted TTF font file, or an empty string if unsuccessful.
     */
    public static function convert($attachmentId): string
    {
        $woffFontFile = get_attached_file($attachmentId);
        $converter    = new self();

        if ($converter->isValidWoffFontFile($woffFontFile)) {
            if (S3Helper::hasS3Support() && S3Helper::isS3Path($woffFontFile)) {
                $ttfFontFile = $converter->createVariantName(
                    $woffFontFile,
                    "ttf"
                );

                $ttfFontFileHttp = S3Helper::restoreS3KeyToHttps(
                    S3Helper::sanitizeS3Key($ttfFontFile)
                );

                if (S3Helper::objectExistsOnS3($ttfFontFile)) {
                    return $ttfFontFileHttp;
                }

                // Create local temp file
                $tempLocalFile = tempnam(sys_get_temp_dir(), 'woff_download_');

                //Download
                S3Helper::downloadFromS3(
                    $woffFontFile,
                    $tempLocalFile
                );

                //Convert and upload
                S3Helper::uploadToS3(
                    $converter->convertLocalWoffToTtf($tempLocalFile),
                    $ttfFontFile
                );

                //Remove local temp file
                unlink($tempLocalFile);

                return $ttfFontFileHttp;
            } else {
                return $converter->convertLocalWoffToTtf($woffFontFile);
            }
        }

        return "";
    }

    /**
     * Converts a local WOFF font file to TTF format.
     *
     * This function utilizes the WoffConverterHelper to perform the conversion
     * and generates a TTF variant name using the createVariantName method.
     *
     * @param string $woffFontFile The path to the local WOFF font file.
     * @return string The path to the converted TTF font file.
     */
    private function convertLocalWoffToTtf($woffFontFile)
    {
        self::convertToTTF(
            $woffFontFile,
            self::createVariantName($woffFontFile, "ttf")
        );
        return self::createVariantName($woffFontFile, "ttf");
    }

    /**
     * Checks if a file is a valid WOFF font file.
     *
     * This function verifies the existence of the file, its non-empty status,
     * and whether its MIME type is 'application/font-woff'.
     *
     * @param string $fontFile The path to the font file being checked.
     * @return bool True if the file is a valid WOFF font, false otherwise.
     */
    private function isValidWoffFontFile($fontFile)
    {
        return !empty($fontFile) && file_exists($fontFile) && in_array(mime_content_type($fontFile), ['application/font-woff', 'application/octet-stream']);
    }

    /**
     * Creates a variant file name based on the provided file name and target suffix.
     *
     * This function extracts the filename from the given path, appends the specified
     * target suffix, and returns the new file name.
     *
     * @param string $fileName      The original file name or path.
     * @param string $targetSuffix  The target suffix to append to the filename.
     * @return string The new variant file name.
     */
    private function createVariantName($fileName, $targetSuffix)
    {
        $pathInfo    = pathinfo($fileName);
        $newFileName = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.' . $targetSuffix;
        return $newFileName;
    }

    /**
     * Unwraps a  WOFF font file and repackages it as an OpenType font and writes it to disk.
     * @param string $woffFile    absolute path of WOFF file (includes filename)
     * @param string $convertPath absolute path to cache converted file (includes filename)
     * @return void
     * @throws \WP_Error
     */
    private function convertToTTF($woffFile, $convertPath): void
    {
        if (is_null($woffFile) || is_null($convertPath)) {
            WoffConverter::bail("Empty argument: convert($woffFile, $convertPath");
        }

        if (! function_exists('gzuncompress')) {
            bail(__CLASS__ . ' requires gzuncompress().');
        }

        $debug            = false;
        $numTables        = 0;
        $oTDirEntrySize   = 16; // bytes
        $oTHeaderSize     = 12;
        $woffDirEntrySize = 20;
        $woffHeaderSize   = 44;

        if (! $fh = fopen($woffFile, "rb")) {
            WoffConverter::bail("Couldn't open file $woffFile.");
        }

        $header = unpack('Nsig/Nflv/Nlen/nntab/nres/Nssize/nmajv/nminv/Nmoff/Nmlen/Nmolen/Nprivo/Nprivl', fread($fh, $woffHeaderSize));

        if ($debug) {
            foreach ($header as $key => $val) {
                printf("%s => 0x%x\n", $key, $val) ;
            }
        }

        foreach ($header as $key => $val) {
            switch ($key) {
                case 'sig':
                    $sfntVersion = $val;
                    if ($debug) {
                        if ($val != 0x774F4646) { // wOFF
                            if ($debug) {
                                echo 'font file not WOFF';
                            }
                            WoffConverter::bail("File is not a valid WOFF font.", $fh);
                        } else {
                            if ($debug) {
                                echo 'font file cool.';
                            }
                        }
                    }
                    break;
                case 'flv':
                    $flavor = $val;
                    if ($val == 0x00010000) {
                        if ($debug) {
                            echo 'TrueType flavor.';
                        }
                    } elseif ($val == 0x4F54544F) {
                        if ($debug) {
                            echo 'CFF flavor.';
                        }
                    } else {
                        if ($debug) {
                            echo 'unknown flavor.';
                        }
                    }
                    // Use otf for all flavors, makes deriving the cache filename easier later on.
                    $fileExtension = 'otf';
                    break;
                case 'len':
                    if ($debug) {
                        echo "\nfile size $val bytes. ";
                    }
                    break;
                case 'ntab':
                    if ($debug) {
                        echo "\n$val font tables.";
                    }
                    $numTables = $val;
                    break;
                case 'res':
                    if ($val != 0) {
                        if ($debug) {
                            echo "\nproblem - reserved field != 0.";
                        }
                        WoffConverter::bail("Reserved field has to be 0 (zero).", $fh);
                    }
                    break;
                case 'ssize':
                    if ($debug) {
                        echo "\ntotal data size $val.";
                    }
                    break;
                case 'majv':
                    if ($debug) {
                        echo "\nmajor version $val.";
                    }
                    break;
                case 'minv':
                    if ($debug) {
                        echo "\nminor version $val.";
                    }
                    break;
                case 'moff':
                    if ($debug) {
                        echo "\nmeta offset $val bytes.";
                    }
                    break;
                case 'mlen':
                    if ($debug) {
                        echo "\ncompressed data block $val bytes";
                    }
                    break;
                case 'molen':
                    if ($debug) {
                        echo "\nuncompressed data block $val bytes";
                    }
                    break;
                case 'privo':
                    if ($debug) {
                        echo "\nprivate data offset $val bytes";
                    }
                    $privateOffset = $val;
                    break;
                case 'privl':
                    if ($debug) {
                        echo "\nprivate data length $val bytes";
                    }
                    $privateLength = $val;
                    break;
            }
        }

        // check out the private data
        if ($debug) {
            $whereAt = ftell($fh);
            fseek($fh, $privateOffset);
            $privateData = fread($fh, $privateLength);
            echo "Private data: $privateData";
            fseek($fh, $whereAt);
        }

        // write offset table
        if ($debug) {
            echo "\nout file $convertPath";
        }
        $ofh = fopen($convertPath, "wb");
        if (! $ofh) {
            WoffConverter::bail("Couldn't open file $outfile for writing.", $fh);
        }
        fwrite($ofh, pack('N', $flavor));
        fwrite($ofh, pack('n', $numTables));

        $maxPower2 = 0;
        while (pow(2, $maxPower2) <= $numTables) {
            $maxPower2++;
        }

        $searchRange   = $maxPower2 * 16;
        $entrySelector = log($searchRange, 2);

        fwrite($ofh, pack('n', $searchRange));
        fwrite($ofh, pack('n', $entrySelector));
        fwrite($ofh, pack('n', $numTables * $searchRange));

        $tableDirectorySize = $oTDirEntrySize * $numTables;

        $tableData   = array();
        $tableLength = array();
        $currentEOF  = $tableDirectorySize + $oTHeaderSize;



        // Write table records
        for ($i = 0; $i < $numTables; $i++) {
            $dirEntry = unpack('Ntag/N4', fread($fh, $woffDirEntrySize));
            if ($debug) {
                printf("\ntag value: %d", $dirEntry['tag']);
                printf("offset: 0x%x\n", $dirEntry[1]);
                printf("compressed size: 0x%x\n", $dirEntry[2]);
                printf("uncompressed size: 0x%x\n", $dirEntry[3]);
                printf("checksum: 0x%x\n", $dirEntry[4]);
            }
            $whereAt = ftell($fh);

            if (fseek($fh, $dirEntry[1])) {
                WoffConverter::bail("fseek-ing to offset {$dirEntry[1]}", $fh, $ofh);
            }

            $tableData[$i] = fread($fh, $dirEntry[2]);

            fseek($fh, $whereAt);
            if ($dirEntry[2] != $dirEntry[3]) {
                $tableData[$i]   = gzuncompress($tableData[$i]);
                $tableLength[$i] = strlen($tableData[$i]);
            } else {
                $tableLength[$i] = $dirEntry[3];
            }

            fwrite($ofh, pack('N', $dirEntry['tag'])); //tag
            fwrite($ofh, pack('N', $dirEntry[4])); // checksum
            fwrite($ofh, pack('N', $currentEOF)); // offset
            fwrite($ofh, pack('N', $tableLength[$i])); // length without padding


            if ($debug) {
                printf("\nwriting table rec for tag: %d\n", $dirEntry['tag']);
                printf("\n\t checksum: 0x%x\n", $dirEntry[4]);
                printf("\n\t offset: %d", $currentEOF);
                printf("\n\t length: %d", $tableLength[$i]);
            }

            $pad = 0;
            if (($tableLength[$i] % 4) != 0) { // pad for 4-byte boundaries
                $pad = $tableLength[$i] % 4;
            }
            $currentEOF += strlen($tableData[$i]) + $pad;
        }



        $bytesWrote = 0;
        // Write table data
        for ($i = 0; $i < count($tableData); $i++) {
            if ($debug) {
                echo "\nfile pointer at: " . ftell($ofh);
                echo "\ntablelength = " . $tableLength[$i] . ' strlen of data: ' . strlen($tableData[$i]);
            }
            $bytesWrote +=     fwrite($ofh, $tableData[$i], $tableLength[$i]);
            if ($debug) {
                echo "\nwriting {$tableLength[$i]} bytes. Actual total bytes written: $bytesWrote";
            }

            if (($tableLength[$i] % 4) != 0) { // pad for 4-byte boundaries
                $pad         = $tableLength[$i] % 4;
                $bytesWrote += fwrite(($ofh), pack("x$pad"));
                if ($debug) {
                    echo "\nwriting $pad null bytes. Actual total bytes written: $bytesWrote";
                }
            }
        }



        fclose($fh);
        fclose($ofh);
    }


    /**
     * Throws an exception with $message and closes given file handles if applicable.
     * @param string $message
     * @param Resource $fh
     * @param Resource $ofh
     */
    private static function bail($message, $fh = null, $ofh = null)
    {
        if (is_resource($fh)) {
            close($fh);
        }
        if (is_resource($ofh)) {
            close($ofh);
        }
        throw new \WP_Error($message);
    }
}
