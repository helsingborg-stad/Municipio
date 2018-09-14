<?php
require_once("../lib/shortpixel-php-req.php");

ShortPixel\setKey("<YOUR API KEY HERE>");
$tmpFolder = tempnam(sys_get_temp_dir(), "shortpixel-php");
echo("Temp folder: " . $tmpFolder);
if(file_exists($tmpFolder)) unlink($tmpFolder);
mkdir($tmpFolder);
\ShortPixel\fromUrls("https://shortpixel.com/img/tests/wrapper/shortpixel.png")->refresh()->wait(300)->toFiles($tmpFolder);
echo("\nSuccessfully saved the optimized image from URL to temp folder.\n");
\ShortPixel\fromFile(__DIR__ . "/data/cc.jpg")->refresh()->wait(300)->toFiles($tmpFolder);
echo("\nSuccessfully saved the optimized image from path to temp folder.\n\n");
