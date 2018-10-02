<?php

if (!getenv("SHORTPIXEL_KEY")) {
    exit("Set the SHORTPIXEL_KEY environment variable.\n");
}

require_once("../vendor/autoload.php");

class ClientIntegrationTest extends PHPUnit_Framework_TestCase {
    static private $tempDir;

    static public function setUpBeforeClass() {
    }

    protected function setUp() {
        //options to defaults
        \ShortPixel\ShortPixel::setOptions(array(
            "lossy" => 1, // 1 - lossy, 2 - glossy, 0 - lossless
            "keep_exif" => 0, // 1 - EXIF is preserved, 0 - EXIF is removed
            "resize" => 0, // 0 - don't resize, 1 - outer resize, 3 - inner resize
            "resize_width" => null, // in pixels. null means no resize
            "resize_height" => null, // in pixels. null means no resize
            "cmyk2rgb" => 1, // convert CMYK to RGB: 1 yes, 0 no
            "convertto" => "", // if '+webp' then also the WebP version will be generated
            // **** return options ****
            "notify_me" => null, // should contain full URL of of notification script (notify.php) - to be implemented
            "wait" => 30, // seconds
            // **** local options ****
            "total_wait" => 30, //seconds
            "base_url" => null, // base url of the images - used to generate the path for toFile by extracting from original URL and using the remaining path as relative path to base_path
            "base_source_path" => "", // base path of the local files
            "base_path" => false, // base path to save the files
            "backup_path" => false, // backup path, relative to the optimization folder (base_source_path)
            // **** persist options ****
            "persist_type" => null, // null - don't persist, otherwise "text" (.shortpixel text file in each folder), "exif" (mark in the EXIF that the image has been optimized) or "mysql" (to be implemented)
            "persist_name" => ".shortpixel"
        ));
        \ShortPixel\setKey(getenv("SHORTPIXEL_KEY"));

        $tmp = tempnam(sys_get_temp_dir(), "shortpixel-php");
        if(file_exists($tmp)) unlink($tmp);
        mkdir($tmp);
        if (is_dir($tmp)) {
            self::$tempDir = $tmp;
        }
    }

    /**
     * This is for Git commits, to make sure that we don't inavertently commit the -dev API urls.
     */
    public function testUseProductionAPI() {
        $this->assertEquals("https://api.shortpixel.com", \ShortPixel\Client::API_URL());
    }

    public function testShouldCompressFromFile() {
        $unoptimizedPath = __DIR__ . "/data/shortpixel.png";
        $result = \ShortPixel\fromFiles($unoptimizedPath)->refresh()->wait(300)->toFiles(self::$tempDir);

        if(count($result->succeeded)) {
            $data = $result->succeeded[0];
            $savedFile = $data->SavedFile;
            $size = filesize($savedFile);
            $contents = fread(fopen($savedFile, "rb"), $size);

            $this->assertEquals($data->LossySize, $size);

            // removes EXIF
            $this->assertNotContains("Copyright ShortPixel", $contents);
        } elseif(count($result->same)) {
            $this->throwException(new Exception("Optimized image is same size and shouldn't"));
        } elseif(count($result->pending)) {
            echo("LossyFromURL - did not finish");
        } else {
            $this->throwException(new Exception("Failed"));
        }

        \ShortPixel\delTree(self::$tempDir);
    }

    public function testShouldCompressFromFileLossless() {
        $unoptimizedPath = __DIR__ . "/data/shortpixel.png";
        $result = \ShortPixel\fromFiles($unoptimizedPath)->refresh()->wait(300)->optimize(0)->toFiles(self::$tempDir);

        if(count($result->succeeded)) {
            $data = $result->succeeded[0];
            $savedFile = $data->SavedFile;
            $size = filesize($savedFile);
            $contents = fread(fopen($savedFile, "rb"), $size);

            $this->assertEquals($data->LoselessSize, $size);

            // removes EXIF
            $this->assertNotContains("Copyright ShortPixel", $contents);
        } elseif(count($result->same)) {
            $this->throwException(new Exception("Losslessly optimized image is same size and shouldn't"));
        } elseif(count($result->pending)) {
            echo("LosslessFromURL - did not finish");
        } else {
            $this->throwException(new Exception("Failed"));
        }

        \ShortPixel\delTree(self::$tempDir);
    }

    public function testShouldCompressFromFileSaveDifferentName() {
        $unoptimizedPath = __DIR__ . "/data/shortpixel.png";
        $result = \ShortPixel\fromFiles($unoptimizedPath)->refresh()->wait(300)->toFiles(self::$tempDir, "shortpixel-new.png");

        if(count($result->succeeded)) {
            $data = $result->succeeded[0];
            $savedFile = $data->SavedFile;
            $this->assertEquals('shortpixel-new.png', basename($savedFile));
            $size = filesize($savedFile);
            $contents = fread(fopen($savedFile, "rb"), $size);

            $this->assertEquals($data->LossySize, $size);
            // removes EXIF
        } elseif(count($result->same)) {
            $this->throwException(new Exception("Optimized image is same size and shouldn't"));
        } elseif(count($result->pending)) {
            echo("LossyFromURL - did not finish");
        } else {
            $this->throwException(new Exception("Failed"));
        }

        \ShortPixel\delTree(self::$tempDir);
    }

    public function testShouldCompressFromFileWithBackup() {
        $unoptimizedPath = __DIR__ . "/data/shortpixel.png";
        $result = \ShortPixel\fromFiles($unoptimizedPath)->refresh()->wait(300)->toFiles(self::$tempDir, null, self::$tempDir . '/ShortPixelBackups');

        if(count($result->succeeded)) {
            $data = $result->succeeded[0];
            $savedFile = $data->SavedFile;
            $size = filesize($savedFile);
            $contents = fread(fopen($savedFile, "rb"), $size);

            $this->assertEquals($data->LossySize, $size);

            $origSize = filesize($unoptimizedPath);
            $bkSize = filesize(self::$tempDir . '/ShortPixelBackups/' . basename($unoptimizedPath));

            $this->assertEquals($origSize, $bkSize);

            // removes EXIF
            $this->assertNotContains("Copyright ShortPixel", $contents);
        } elseif(count($result->same)) {
            $this->throwException(new Exception("Optimized image is same size and shouldn't"));
        } elseif(count($result->pending)) {
            echo("LossyFromURL - did not finish");
        } else {
            $this->throwException(new Exception("Failed"));
        }

        \ShortPixel\delTree(self::$tempDir);
    }

    public function testShouldCompressFromFiles() {
        $unoptimizedPath = __DIR__ . "/data/shortpixel.png";
        $unoptimizedPath2 = __DIR__ . "/data/cc.jpg";
        $result = \ShortPixel\fromFiles(array($unoptimizedPath, $unoptimizedPath2))->refresh()->wait(300)->toFiles(self::$tempDir);

        if(count($result->succeeded) == 2) {
            $data = $result->succeeded[0];
            $savedFile = $data->SavedFile;
            $size = filesize($savedFile);
            $contents = fread(fopen($savedFile, "rb"), $size);

            $this->assertEquals($data->LossySize, $size);

            // removes EXIF
            $this->assertNotContains("Copyright ShortPixel", $contents);
        } elseif(count($result->failed)) {
            $this->throwException(new Exception("Failed"));
        } elseif(count($result->same)) {
            $this->throwException(new Exception("Optimized image is same size and shouldn't"));
        } elseif(count($result->pending)) {
            echo("LossyFromURL - did not finish");
        }
        \ShortPixel\delTree(self::$tempDir);
    }

    public function testShouldNotCompressFromFolderWithoutPersister() {
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => null));
        $folderPath = __DIR__ . "/data/images1";
        try {
            \ShortPixel\fromFolder($folderPath);
            $this->throwException(new Exception("Persist is not set up but fromFolder did not throw the Persist exception."));
        } catch (\ShortPixel\PersistException $ex) {
            echo("PersistException thrown.");
        }
    }

    public function testShouldGracefullyFixCorruptedTextPersisterFile() {
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => "text"));
        $sourceFolder = __DIR__ . "/data/txt-persist-corrupt";
        $folderPath = self::$tempDir;
        try {
            \ShortPixel\recurseCopy($sourceFolder, $folderPath);
            $cmd = \ShortPixel\fromFolder($folderPath);
            $files = $cmd->getData()["files"];
            $this->assertEquals(1, count($files));
            $this->assertEquals(substr($files[0], -24), "c3rgfb8dr5xyjcgx3o1w.jpg");
        } finally {
            \ShortPixel\delTree(self::$tempDir);
        }
    }

    public function testShouldLockFolderTextPersister() {
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => "text"));
        $sourceFolder = __DIR__ . "/data/images-jpg";
        $folderPath = self::$tempDir;
        try {
            $processId = uniqid("CLI");
            $splock = new \ShortPixel\Lock($processId, $folderPath);
            $splock->setTimeout(1);
            $splock->lock();
            sleep(3);
            $processId = uniqid("CLI");
            $splock = new \ShortPixel\Lock($processId, $folderPath);
            //this one should work as the previous lock timeout is 1 sec.
            $splock->lock();
            //try to lock again, it should not work as this lock is 360sec
            $processId = uniqid("CLI");
            $splock = new \ShortPixel\Lock($processId, $folderPath);
            $splock->lock();
            $this->assertEquals("Was successful and shouldn't", "Lock twice");
        } catch( \Exception $ex) {
            if($ex->getCode = -19) {
                $this->assertTrue(true);
            } else {
                $this->assertTrue(false);
            }
        } finally {
            \ShortPixel\delTree(self::$tempDir);
        }
    }

    public function testShouldNotSelectMoreMbWithTextPersister() {
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => "text"));
        $sourceFolder = __DIR__ . "/data/big-pngs";
        $folderPath = self::$tempDir;
        try {
            \ShortPixel\recurseCopy($sourceFolder, $folderPath);
            $cmd = \ShortPixel\fromFolder($folderPath, \ShortPixel\ShortPixel::MAX_ALLOWED_FILES_PER_CALL, array(), false, 16); // not more than 16MB
            $files = $cmd->getData()["files"];
            $this->assertEquals(1, count($files));
            $this->assertEquals(substr($files[0], -10), "_00019.png");
        } finally {
            \ShortPixel\delTree(self::$tempDir);
        }
    }

    public function testFolderInfoWithTextPersister() {
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => "text"));
        //$sourceFolder = __DIR__ . "/data/images-many";
        $sourceFolder = __DIR__ . "/data/folder-in-progress";
        $folderPath = self::$tempDir;
        try {
            \ShortPixel\recurseCopy($sourceFolder, $folderPath);
            $result = \ShortPixel\folderInfo($folderPath);
            //$this->assertEquals(0, $result->succeeded);
            //$this->assertEquals(24, $result->pending);
            $this->assertEquals(28, $result->total);
            $this->assertEquals(6, $result->succeeded);
            $this->assertEquals(22, $result->pending);
        } finally {
            \ShortPixel\delTree(self::$tempDir);
        }
    }

    public function testFolderInfoRecurseDepthWithTextPersister() {
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => "text"));
        //$sourceFolder = __DIR__ . "/data/images-many";
        $sourceFolder = __DIR__ . "/data/folder-tree";
        $folderPath = self::$tempDir;
        try {
            \ShortPixel\recurseCopy($sourceFolder, $folderPath);
            $result = \ShortPixel\folderInfo($folderPath,true, false, array(), false, 0);
            $this->assertEquals(5, $result->total);
            $result = \ShortPixel\folderInfo($folderPath,true, false, array(), false, 1);
            $this->assertEquals(11, $result->total);
            $result = \ShortPixel\folderInfo($folderPath,true, false, array(), false, 2);
            $this->assertEquals(16, $result->total);
            $result = \ShortPixel\folderInfo($folderPath,true, false, array(), false);
            $this->assertEquals(28, $result->total);
        } finally {
            \ShortPixel\delTree(self::$tempDir);
        }
    }

    public function testShouldCompressLossyFromUrl() {
        \ShortPixel\ShortPixel::log("RUN TEST: " . __FUNCTION__);
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => null));
        //$result = \ShortPixel\fromUrls("https://blog.degruyter.com/wp-content/uploads/2017/10/unsafe-valley.png")->refresh()->wait(300)->toFiles(self::$tempDir);
        $result = \ShortPixel\fromUrls("https://shortpixel.com/img/tests/wrapper/shortpixel.png")->refresh()->wait(300)->toFiles(self::$tempDir);
        //$result2 = ShortPixel\fromUrls('https://i.imgur.com/0B5pHiU.png')->toFiles(self::$tempDir);

        if(count($result->succeeded)) {
            $data = $result->succeeded[0];
            $savedFile = $data->SavedFile;
            $size = filesize($savedFile);
            $contents = fread(fopen($savedFile, "rb"), $size);

            $this->assertEquals($data->LossySize, $size);

            // removes EXIF
            $this->assertNotContains("Copyright ShortPixel", $contents);
        } elseif(count($result->same)) {
            $this->throwException("Optimized image is same size and shouldn't");
        } elseif(count($result->pending)) {
            echo("LossyFromURL - did not finish");
        } else {
            $this->throwException("Failed");
        }
        \ShortPixel\delTree(self::$tempDir);
    }

    public function testShouldCompressJpegActuallyPngFromUrl() {
        \ShortPixel\ShortPixel::log("RUN TEST: " . __FUNCTION__);
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => null));
        $result = \ShortPixel\fromUrls("https://shortpixel.com/img/tests/wrapper/actually-png.jpg")->refresh()->wait(300)->toFiles(self::$tempDir);

        if(count($result->succeeded)) {
            $data = $result->succeeded[0];
            $savedFile = $data->SavedFile;
            $size = filesize($savedFile);
            $contents = fread(fopen($savedFile, "rb"), $size);

            $this->assertEquals($data->LossySize, $size);

        } elseif(count($result->same)) {
            $this->throwException("Optimized image is same size and shouldn't");
        } elseif(count($result->pending)) {
            echo("LossyFromURL - did not finish");
        } else {
            $this->throwException("Failed");
        }
        \ShortPixel\delTree(self::$tempDir);
    }

    public function testShouldCompressLossyFromUrls()
    {
        \ShortPixel\ShortPixel::log("RUN TEST: " . __FUNCTION__);
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => null));
        $source = \ShortPixel\fromUrls(array(
            "https://shortpixel.com/img/tests/wrapper/cc2.jpg",
            "https://shortpixel.com/img/tests/wrapper/shortpixel.png?fake=name.1234",
            "https://shortpixel.com/img/tests/wrapper/cc.jfif",
            "http://dev.shortpixel.com/test-image-with-no-extension"
        ));
        $result = $source->refresh()->wait(300)->toFiles(self::$tempDir);

        $this->assertEquals(4, count($result->succeeded) + count($result->pending));
        \ShortPixel\delTree(self::$tempDir);
    }

    public function testShouldResizeJpg() {
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => null));
        $source = \ShortPixel\fromUrls("https://shortpixel.com/img/tests/wrapper/cc-resize.jpg");
        //$result = $source->resize(50, 50)->toFiles(self::$tempDir);
        $result = $source->refresh()->resize(100, 100)->wait(120)->toFiles(self::$tempDir);

        if(count($result->succeeded)) {
            $data = $result->succeeded[0];
            $savedFile = $data->SavedFile;
            $size = filesize($savedFile);

            // size is correct
            $this->assertEquals($data->LossySize, filesize($savedFile));
            // width == 100
            $imageSize = getimagesize($savedFile);
            $this->assertEquals(100, min($imageSize[0], $imageSize[1]));
            //EXIF is removed
            $exif = exif_read_data($savedFile);
            $this->assertNotContains("EXIF", $exif['SectionsFound']);
        } elseif(count($result->same)) {
            $this->throwException("Optimized image is same size and shouldn't");
        } elseif(count($result->pending)) {
            $this->throwException("testShouldResizeJpg - did not finish");
        } else {
            $this->throwException("Failed");
        }
        \ShortPixel\delTree(self::$tempDir);
    }

    public function testShouldResizeInnerJpg() {
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => null));
        $source = \ShortPixel\fromUrls("https://shortpixel.com/img/tests/wrapper/cc-resize.jpg");
        //$result = $source->resize(50, 50)->toFiles(self::$tempDir);
        $result = $source->refresh()->resize(200, 200, true)->wait(120)->toFiles(self::$tempDir);

        if(count($result->succeeded)) {
            $data = $result->succeeded[0];
            $savedFile = $data->SavedFile;
            $size = filesize($savedFile);

            // size is correct
            $this->assertEquals($data->LossySize, filesize($savedFile));
            // width == 200
            $imageSize = getimagesize($savedFile);
            $this->assertEquals(200, max($imageSize[0], $imageSize[1]));
            //EXIF is removed
            $exif = exif_read_data($savedFile);
            $this->assertNotContains("EXIF", $exif['SectionsFound']);
        } elseif(count($result->same)) {
            $this->throwException("Optimized image is same size and shouldn't");
        } elseif(count($result->pending)) {
            $this->throwException("testShouldResizeJpg - did not finish");
        } else {
            $this->throwException("Failed");
        }
        \ShortPixel\delTree(self::$tempDir);
    }

    public function testShouldPreserveExifJpg() {
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => null));
        $source = \ShortPixel\fromUrls("https://shortpixel.com/img/tests/wrapper/cc.jpg");
        $result = $source->refresh()->keepExif()->wait(90)->toFiles(self::$tempDir);

        if(count($result->succeeded)) {
            $data = $result->succeeded[0];
            $savedFile = $data->SavedFile;
            $size = filesize($savedFile);

            // size is correct
            $this->assertEquals($data->LossySize, filesize($savedFile));
            //EXIF is removed
            $exif = exif_read_data($savedFile);
            $this->assertContains("EXIF", $exif['SectionsFound']);
        } elseif(count($result->same)) {
            $this->throwException("Optimized image is same size and shouldn't");
        } elseif(count($result->pending)) {
            $this->throwException("testShouldPreserveExifJpg - did not finish");
        } else {
            $this->throwException("Failed");
        }
        \ShortPixel\delTree(self::$tempDir);
    }

    public function testShoulGenerateWebPFromJpg() {
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => null));
        $unoptimizedPath = __DIR__ . "/data/cc2.jpg";
        $source = \ShortPixel\fromFiles($unoptimizedPath);
        $result = $source->refresh()->generateWebP()->wait(120)->toFiles(self::$tempDir);

        if(count($result->succeeded)) {
            $data = $result->succeeded[0];
            $savedFile = $data->WebPSavedFile;

            // size is correct
            if($data->WebPLossySize == 'NA' || !$data->WebPLossySize) {
                $data->lala = 1;
            }
            $this->assertEquals($data->WebPLossySize, filesize($savedFile));
        } elseif(count($result->same)) {
            $this->throwException("Optimized image is same size and shouldn't");
        } elseif(count($result->pending)) {
            $this->throwException("testShoulGenerateWebPFromJpg - did not finish");
        } else {
            $this->throwException("Failed");
        }
        \ShortPixel\delTree(self::$tempDir);
    }

    public function testShoulGenerateWebPFromJpgUrl() {
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => null));
        $source = \ShortPixel\fromUrls("https://shortpixel.com/img/tests/wrapper/cc4.jpg");
        $result = $source->refresh()->generateWebP()->wait(120)->toFiles(self::$tempDir);

        if(count($result->succeeded)) {
            $data = $result->succeeded[0];
            $savedFile = $data->WebPSavedFile;

            // size is correct
            $this->assertEquals($data->WebPLossySize, filesize($savedFile));
        } elseif(count($result->same)) {
            $this->throwException("Optimized image is same size and shouldn't");
        } elseif(count($result->pending)) {
            $this->throwException("testShoulGenerateWebPFromJpgUrl - did not finish");
        } else {
            $this->throwException("Failed");
        }
        \ShortPixel\delTree(self::$tempDir);
    }

    public function testShoulRefreshFromURL() {
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => null));
        //use same URL but switch files inbetween
        $sourceURL = "https://shortpixel.com/img/tests/wrapper/lizzard5.jpg";

        //first optimize the clean image
        file_get_contents("https://shortpixel.com/switch-test-imgs/clean");
        $result = \ShortPixel\fromUrls($sourceURL)->refresh()->wait(120)->toFiles(self::$tempDir);
        if(count($result->succeeded)) {
            $data = $result->succeeded[0];
            $savedFileSize = getimagesize($data->SavedFile);
        } else {
            $this->throwException(new \Exception("testShoulRefreshFromURL failed to optimize the image 1."));
        }

        //then ask to refresh - should optimize the watermarked image
        file_get_contents("https://shortpixel.com/switch-test-imgs/iw");
        sleep(5);
        $result2 = \ShortPixel\fromUrls($sourceURL)->refresh()->wait(120)->toFiles(self::$tempDir);

        if(count($result2->succeeded)) {
            $data2 = $result2->succeeded[0];
            $savedFileSize2 = getimagesize($data2->SavedFile);
        } else {
            $this->throwException(new \Exception("testShoulRefreshFromURL failed to optimize the image 2."));
        }

        // the clean image is 1025px wide
        $this->assertEquals(1025,$savedFileSize[0]);
        // the watermarked image is 1024px wide
        $this->assertEquals(1024,$savedFileSize2[0]);

        \ShortPixel\delTree(self::$tempDir);
    }

    public function testShouldReturnInaccessibleURL() {
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => null));
        $source = \ShortPixel\fromUrls("https://shortpixel.com/img/not-present.jpg");
        $result = $source->toFiles(self::$tempDir);

        //TO.DO remove the -106, it's a hack
        if(!count($result->failed) || ($result->failed[0]->Status->Code != -203 /* && $result->failed[0]->Status->Code != -106 */)) {
            throw new \ShortPixel\ClientException("Image does not exist but did not show up as failed (-203).");
        }
    }

    public function testShouldReturnTooManyURLs() {
        $tooMany = array();
        for($i = 0; $i < 101; $i++) {
            $tooMany[] = "https://shortpixel.com/img/not-present{$i}.jpg";
        }
        try {
            \ShortPixel\fromUrls($tooMany);
        }
        catch (\ShortPixel\ClientException $ex) {
            return;
        }
        throw new \ShortPixel\ClientException("More than 100 images but no exception thrown.");
    }

    public function testShouldReturnQuotaExceeded() {
        \ShortPixel\setKey('1ek71vnK0Xok3S2B3VYQ'); //this is a key with 0 credits
        try {
            $source = \ShortPixel\fromUrls("https://shortpixel.com/img/tests/wrapper/quota-exceed.jpg");
            $source->toFiles(self::$tempDir);
        } catch(\ShortPixel\AccountException $ex) {
            $this->assertEquals(-403, $ex->getCode());
            if($ex->getCode() == -403) {
                return;
            }
        } finally {
            \ShortPixel\setKey(getenv("SHORTPIXEL_KEY")); //put back the right key, for the next tests...
        }
        throw new \ShortPixel\ClientException("No Quota Exceeded message.");
    }

    public function testShouldCompressJPGsFromFolderWithTextPersister() {
        \ShortPixel\ShortPixel::log("RUN TEST: " . __FUNCTION__);
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => "text"));
        $sourceFolder = __DIR__ . "/data/images-jpg";
        $folderPath = self::$tempDir;
        try {
            \ShortPixel\recurseCopy($sourceFolder, $folderPath);
            $result = \ShortPixel\fromFolder($folderPath)->wait(300)->toFiles($folderPath);

            if(count($result->succeeded) > 0) {
                foreach($result->succeeded as $res) {
                    $this->assertTrue(\ShortPixel\isOptimized($res->SavedFile));
                }
            }
            if(count($result->failed)) {
                $this->throwException(new Exception("Failed"));
            }
            if(count($result->same)) {
                $this->throwException(new Exception("Optimized image is same size and shouldn't"));
            }
            if(count($result->pending)) {
                echo("LossyFromURL - did not finish");
            }
        } finally {
            \ShortPixel\delTree(self::$tempDir);
        }
    }

    public function testShouldNotifyProgressFromFolderWithTextPersister() {
        \ShortPixel\ShortPixel::log("RUN TEST: " . __FUNCTION__);
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => "text", "notify_progress" => true));
        $sourceFolder = __DIR__ . "/data/images-jpg";
        $folderPath = self::$tempDir;
        try {
            \ShortPixel\recurseCopy($sourceFolder, $folderPath);
            $info = \ShortPixel\folderInfo($folderPath);
            $notifier = \ShortPixel\notify\ProgressNotifier::constructNotifier($folderPath);
            $notifier->recordProgress($info, true);

            \ShortPixel\fromFolder($folderPath)->wait(300)->toFiles($folderPath);

            $progress = $notifier->getData();
            $this->assertEquals(5, $progress->succeeded);
            foreach($progress->succeededList as $val) {
                $this->assertEquals("Success", $val->Status->Message);
            }

        } finally {
            \ShortPixel\delTree(self::$tempDir);
            \ShortPixel\ShortPixel::setOptions(array("notify_progress" => false));
        }
    }

    public function testShouldCompressManyFromFolderWithTextPersister() {
        \ShortPixel\ShortPixel::log("RUN TEST: " . __FUNCTION__);
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => "text"));
        $sourceFolder = __DIR__ . "/data/images-many";
        $folderPath = self::$tempDir;
        try {
            \ShortPixel\recurseCopy($sourceFolder, $folderPath);

            $imageCount = 0;
            $tries = 0;

            while($imageCount < 24 && $tries < 5) {
                $result = \ShortPixel\fromFolder($folderPath)->wait(300)->toFiles($folderPath);
                $tries++;

                if(count($result->succeeded) > 0) {
                    $imageCount += count($result->succeeded);
                } elseif(count($result->failed)) {
                    $this->throwException(new Exception("Failed"));
                } elseif(count($result->same)) {
                    $this->throwException(new Exception("Optimized image is same size and shouldn't"));
                } elseif(count($result->pending)) {
                    echo("LossyFromURL - did not finish");
                }
            }
            $this->assertEquals(24, $imageCount);
        } finally {
            \ShortPixel\delTree($folderPath);
        }
    }

    public function testShouldExcludeSubfolderWithTextPersister() {
        \ShortPixel\ShortPixel::log("RUN TEST: " . __FUNCTION__);
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => "text"));
        $sourceFolder = __DIR__ . "/data/images-subfolders";
        $folderPath = self::$tempDir;
        try {
            \ShortPixel\recurseCopy($sourceFolder, $folderPath);

            $imageCount = 0;
            $tries = 0;

            while($tries < 10) {
                $result = \ShortPixel\fromFolder($folderPath, \ShortPixel\ShortPixel::MAX_ALLOWED_FILES_PER_CALL, array("sub", "sub2"))->wait(300)->toFiles($folderPath);
                $tries++;

                if(count($result->succeeded) > 0) {
                    $imageCount += count($result->succeeded);
                } elseif(count($result->failed)) {
                    $this->throwException(new Exception("Failed"));
                } elseif(count($result->same)) {
                    $this->throwException(new Exception("Optimized image is same size and shouldn't"));
                } elseif(count($result->pending)) {
                    echo("LossyFromURL - did not finish");
                }
            }
            $this->assertEquals(22, $imageCount);
        } finally {
            \ShortPixel\delTree($folderPath);
        }
    }

    public function testShouldCacheSubFolderGetTodo() {
        \ShortPixel\ShortPixel::log("RUN TEST: " . __FUNCTION__);
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => "text", "cache_time" => 1800));
        $sourceFolder = __DIR__ . "/data/images-subfolder";
        $folderPath = self::$tempDir;
        try {
            \ShortPixel\recurseCopy($sourceFolder, $folderPath);

            $imageCount = 0;
            $tries = 0;

            $cache = \ShortPixel\SPCache::Get();
            $cache->delete(self::$tempDir . '/sub');
            $cached = $cache->fetch(self::$tempDir . '/sub');
            $this->assertFalse($cached);

            while($tries < 10) {
                $result = \ShortPixel\fromFolder($folderPath, \ShortPixel\ShortPixel::MAX_ALLOWED_FILES_PER_CALL)->wait(300)->toFiles($folderPath);
                $tries++;

                if(count($result->succeeded) > 0) {
                    $imageCount += count($result->succeeded);
                } elseif(count($result->failed)) {
                    $this->throwException(new Exception("Failed"));
                } elseif(count($result->same)) {
                    $this->throwException(new Exception("Optimized image is same size and shouldn't"));
                } elseif(count($result->pending)) {
                    echo("LossyFromURL - did not finish");
                }
            }
            $this->assertEquals(4, $imageCount);
            $cached = $cache->fetch(self::$tempDir . '/sub');
            $this->assertNotFalse($cached);


        } finally {
            \ShortPixel\delTree($folderPath);
        }
    }

    public function testShouldReoptimizeChangedFileWithTextPersister() {
        \ShortPixel\ShortPixel::log("RUN TEST: " . __FUNCTION__);
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => "text"));
        $sourceFolder = __DIR__ . "/data/images-changed";
        $folderPath = self::$tempDir;
        try {
            \ShortPixel\recurseCopy($sourceFolder, $folderPath);
            $cmd = \ShortPixel\fromFolder($folderPath);
            $files = $cmd->getData()["files"];
            $this->assertEquals(1, count($files));
        } finally {
            \ShortPixel\delTree(self::$tempDir);
        }
    }


    public function testShouldReoptimizeChangedFileWithTextPersisterDifferentTarget() {
        \ShortPixel\ShortPixel::log("RUN TEST: " . __FUNCTION__);
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => "text"));
        $sourceFolder = __DIR__ . "/data/images-changed-diff-target";
        $folderPath = self::$tempDir;
        try {
            \ShortPixel\recurseCopy($sourceFolder, $folderPath);
            $cmd = \ShortPixel\fromFolder($folderPath . "/source", \ShortPixel\ShortPixel::MAX_ALLOWED_FILES_PER_CALL, array(), $folderPath . "/optimized");
            $files = $cmd->getData()["files"];
            $this->assertEquals(1, count($files));
        } finally {
            \ShortPixel\delTree(self::$tempDir);
        }
    }


    public function testShouldCompressRecurseDepthWithTextPersister() {
        \ShortPixel\ShortPixel::log("RUN TEST: " . __FUNCTION__);
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => "text"));
        $sourceFolder = __DIR__ . "/data/folder-tree";
        $folderPath = self::$tempDir;
        try {
            \ShortPixel\recurseCopy($sourceFolder, $folderPath);

            $imageCount = 0;

            $tries = 0;
            while($tries < 5) {
                $result = \ShortPixel\fromFolder($folderPath, \ShortPixel\ShortPixel::MAX_ALLOWED_FILES_PER_CALL, array(),false, \ShortPixel\ShortPixel::CLIENT_MAX_BODY_SIZE, 0)->wait(300)->toFiles($folderPath);
                $tries++;

                if(count($result->succeeded) > 0) {
                    $imageCount += count($result->succeeded);
                } elseif(count($result->failed)) {
                    $this->throwException(new Exception("Failed"));
                } elseif(count($result->same)) {
                    $this->throwException(new Exception("Optimized image is same size and shouldn't"));
                } elseif(count($result->pending)) {
                    echo("LossyFromURL - did not finish");
                }
            }
            $this->assertEquals(5, $imageCount);

            $tries = 0;
            while($tries < 10) {
                $result = \ShortPixel\fromFolder($folderPath, \ShortPixel\ShortPixel::MAX_ALLOWED_FILES_PER_CALL, array(),false, \ShortPixel\ShortPixel::CLIENT_MAX_BODY_SIZE, 1)->wait(300)->toFiles($folderPath);
                $tries++;

                if(count($result->succeeded) > 0) {
                    $imageCount += count($result->succeeded);
                } elseif(count($result->failed)) {
                    $this->throwException(new Exception("Failed"));
                } elseif(count($result->same)) {
                    $this->throwException(new Exception("Optimized image is same size and shouldn't"));
                } elseif(count($result->pending)) {
                    echo("LossyFromURL - did not finish");
                }
            }
            $this->assertEquals(11, $imageCount);

            $tries = 0;
            while($tries < 10) {
                $result = \ShortPixel\fromFolder($folderPath, \ShortPixel\ShortPixel::MAX_ALLOWED_FILES_PER_CALL, array(),false, \ShortPixel\ShortPixel::CLIENT_MAX_BODY_SIZE, 2)->wait(300)->toFiles($folderPath);
                $tries++;

                if(count($result->succeeded) > 0) {
                    $imageCount += count($result->succeeded);
                } elseif(count($result->failed)) {
                    $this->throwException(new Exception("Failed"));
                } elseif(count($result->same)) {
                    $this->throwException(new Exception("Optimized image is same size and shouldn't"));
                } elseif(count($result->pending)) {
                    echo("LossyFromURL - did not finish");
                }
            }
            $this->assertEquals(16, $imageCount);
        } finally {
            \ShortPixel\delTree($folderPath);
        }
    }

    public function testShouldCompressManyFromWebFolderWithTextPersister() {
        \ShortPixel\ShortPixel::log("RUN TEST: " . __FUNCTION__);
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => "text"));
        $sourceFolder = __DIR__ . "/data/images-subfolders";
        $sourceWebPath = "http://shortpixel.com/img/tests/wrapper/images-subfolders";
        $folderPath = self::$tempDir;
        try {
            \ShortPixel\recurseCopy($sourceFolder, $folderPath);

            $imageCount = 0;
            $tries = 0;

            while($imageCount < 28 && $tries < 10) {
                $result = \ShortPixel\fromWebFolder($folderPath, $sourceWebPath)->wait(300)->toFiles($folderPath);
                $tries++;

                if(count($result->succeeded) > 0) {
                    $imageCount += count($result->succeeded);
                } elseif(count($result->failed)) {
                    $this->throwException(new Exception("Failed"));
                } elseif(count($result->same)) {
                    $this->throwException(new Exception("Optimized image is same size and shouldn't"));
                } elseif(count($result->pending)) {
                    echo("LossyFromURL - did not finish");
                }
            }
            if(28 != $imageCount) {
                $result->lala = 2;
            }
            $this->assertEquals(28, $imageCount);
        } finally {
            \ShortPixel\delTree($folderPath);
        }
    }

    public function testShouldCompressI18NFromWebFolderWithTextPersister() {
        \ShortPixel\ShortPixel::log("RUN TEST: " . __FUNCTION__);
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => "text"));
        $sourceFolder = __DIR__ . "/data/images-i18n";
        $sourceWebPath = "http://shortpixel.com/img/tests/wrapper/images-i18n";
        $folderPath = self::$tempDir;
        try {
            \ShortPixel\recurseCopy($sourceFolder, $folderPath);

            $imageCount = $imageSame = 0;
            $tries = 0;

            while($imageCount < 6 && $tries < 6) {
                $result = \ShortPixel\fromWebFolder($folderPath, $sourceWebPath)->wait(300)->toFiles($folderPath);
                $tries++;

                if(count($result->succeeded) > 0) {
                    $imageCount += count($result->succeeded);
                }
                if(count($result->failed)) {
                    $this->throwException(new Exception("Failed"));
                }
                if(count($result->same)) {
                    $imageSame += count($result->same);
                } elseif(count($result->pending)) {
                    echo("LossyFromURL - did not finish");
                }
            }
            if(6 != $imageCount) {
                $result->lala = 2;
            }
            $this->assertEquals(6, $imageCount);
        } finally {
            \ShortPixel\delTree($folderPath);
        }
    }

    public function testShouldCompressStrangeFolderNamesFromWebFolderWithTextPersister() {
        \ShortPixel\ShortPixel::log("RUN TEST: " . __FUNCTION__);
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => "text"));
        $sourceFolder = __DIR__ . "/data/images-strange-folders";
        $sourceWebPath = "http://shortpixel.com/img/tests/wrapper/images-strange-folders";
        $folderPath = self::$tempDir;
        try {
            \ShortPixel\recurseCopy($sourceFolder, $folderPath);

            $imageCount = $imageSame = 0;
            $tries = 0;

            while ($imageCount + $imageSame < 29 && $tries < 10) {
                $result = \ShortPixel\fromWebFolder($folderPath, $sourceWebPath)->wait(300)->toFiles($folderPath);
                $tries++;

                if (count($result->succeeded) > 0) {
                    $imageCount += count($result->succeeded);
                }
                if (count($result->failed) > 0) {
                    $this->throwException(new Exception("Failed"));
                }
                if (count($result->same)) {
                    $imageSame += count($result->same);
                } elseif (count($result->pending)) {
                    echo("LossyFromURL - did not finish");
                }
            }
            $this->assertEquals(29, $imageCount + $imageSame);
        } catch (\ShortPixel\ClientException $ex) {
            $this->assertTrue(false);
        } finally {
            \ShortPixel\delTree($folderPath);
        }
    }

    public function testShouldCompressManyFromWebFolderWithTextPersisterDifferentTarget() {
        \ShortPixel\ShortPixel::log("RUN TEST: " . __FUNCTION__);
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => "text"));
        $sourceFolder = __DIR__ . "/data/images-subfolders";
        $sourceWebPath = "http://shortpixel.com/img/tests/wrapper/images-subfolders";
        $folderPath = self::$tempDir;
        $targetPath = tempnam(sys_get_temp_dir(), "shortpixel-php");
        if(file_exists($targetPath)) unlink($targetPath);
        mkdir($targetPath);
        try {
            \ShortPixel\recurseCopy($sourceFolder, $folderPath);

            $imageCount = 0;
            $tries = 0;

            while($imageCount < 28 && $tries < 10) {
                $result = \ShortPixel\fromWebFolder($folderPath, $sourceWebPath, array(), $targetPath)->wait(300)->toFiles($targetPath);
                $tries++;

                if(count($result->succeeded) > 0) {
                    $imageCount += count($result->succeeded);
                } elseif(count($result->failed)) {
                    $this->throwException(new Exception("Failed"));
                } elseif(count($result->same)) {
                    $this->throwException(new Exception("Optimized image is same size and shouldn't"));
                } elseif(count($result->pending)) {
                    echo("LossyFromURL - did not finish");
                }
            }
            if(28 != $imageCount) {
                $result->lala = 2;
            }
            $this->assertEquals(28, $imageCount);
        } finally {
            \ShortPixel\delTree($folderPath);
            \ShortPixel\delTree($targetPath, false);
        }
    }

    public function testShouldCompressSubfolderWithTextPersister() {
        \ShortPixel\ShortPixel::log("RUN TEST: " . __FUNCTION__);
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => "text"));
        $sourceFolder = __DIR__ . "/data/images-subfolders";
        $folderPath = self::$tempDir;
        try {
            \ShortPixel\recurseCopy($sourceFolder, $folderPath);

            $imageCount = 0;
            $tries = 0;
            $allResults = array();

            while($imageCount < 28 && $tries < 10) {
                $result = \ShortPixel\fromFolder($folderPath)->wait(300)->toFiles($folderPath);
                $tries++;

                if(count($result->succeeded) > 0) {
                    $imageCount += count($result->succeeded);
                    $allResults = array_merge($allResults, $result->succeeded);
                } elseif(count($result->failed)) {
                    $this->throwException(new Exception("Failed"));
                } elseif(count($result->same)) {
                    $this->throwException(new Exception("Optimized image is same size and shouldn't"));
                } elseif(count($result->pending)) {
                    echo("LossyFromURL - did not finish");
                }
            }
            $this->assertEquals(28, $imageCount);
            $this->assertTrue(\ShortPixel\isOptimized( $folderPath . "/sub"));
        } finally {
            \ShortPixel\delTree($folderPath);
        }
    }

    public function testShouldCompressSubfolderWithTextPersisterWithBackup() {
        \ShortPixel\ShortPixel::log("RUN TEST: " . __FUNCTION__);
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => "text"));
        $sourceFolder = __DIR__ . "/data/images-subfolders";
        $folderPath = self::$tempDir;
        try {
            \ShortPixel\recurseCopy($sourceFolder, $folderPath);

            $imageCount = 0;
            $tries = 0;
            $allResults = array();

            while($imageCount < 28 && $tries < 10) {
                $result = \ShortPixel\fromFolder($folderPath)->wait(300)->toFiles($folderPath, null, $folderPath . '/ShortPixelBackups');
                $tries++;

                if(count($result->succeeded) > 0) {
                    $imageCount += count($result->succeeded);
                    $allResults = array_merge($allResults, $result->succeeded);
                    //TODO aici sa verificam backups la succeeded
                } elseif(count($result->failed)) {
                    $this->throwException(new Exception("Failed"));
                } elseif(count($result->same)) {
                    $this->throwException(new Exception("Optimized image is same size and shouldn't"));
                } elseif(count($result->pending)) {
                    echo("LossyFromURL - did not finish");
                }
            }
            $this->assertEquals(28, $imageCount);
            if(!\ShortPixel\isOptimized( $folderPath . "/sub")) {
                $result->lala = 2;
            }
            $this->assertTrue(\ShortPixel\isOptimized( $folderPath . "/sub"));
        } finally {
            \ShortPixel\delTree($folderPath);
        }
    }

    public function testShouldReturnPendingWithTextPersister() {
        \ShortPixel\ShortPixel::log("RUN TEST: " . __FUNCTION__);
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => "text"));
        $sourceFolder = __DIR__ . "/data/big-pngs";
        $folderPath = self::$tempDir;
        try {
            \ShortPixel\recurseCopy($sourceFolder, $folderPath);

            $result = \ShortPixel\fromFolder($folderPath)->wait(1)->toFiles($folderPath);

            $this->assertEquals(2, count($result->pending));

        } finally {
            \ShortPixel\delTree($folderPath);
        }
    }

    public function testShouldRetryPendingUsingURLWithTextPersister() {
        \ShortPixel\ShortPixel::log("RUN TEST: " . __FUNCTION__);
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => "text"));
        $sourceFolder = __DIR__ . "/data/big-pngs";
        $folderPath = self::$tempDir;
        try {
            \ShortPixel\recurseCopy($sourceFolder, $folderPath);

            $result = \ShortPixel\fromFolder($folderPath)->wait(1)->toFiles($folderPath);
            $this->assertEquals(2, count($result->pending));

            $result2 = \ShortPixel\fromFolder($folderPath)->wait(300)->toFiles($folderPath);
            if(count($result2->succeeded) < 2) {
                $result2 = \ShortPixel\fromFolder($folderPath)->wait(300)->toFiles($folderPath);
            }

            //OriginalURL should be the same, as the second time it should send using the URL from the .shortpixel file
            //si aici comparam OriginalUrl cu cel de mai sus
            foreach($result->pending as $pend) {
                $match = 0;
                foreach($result2->succeeded as $suc) {
                    if($pend->OriginalURL === $suc->OriginalURL) {
                        $match++;
                    }
                }
                $this->assertEquals(1, $match);
            }

            $this->assertEquals(2, count($result2->succeeded));


        } finally {
            \ShortPixel\delTree($folderPath);
        }
    }

    public function testShouldFallbackExpiredPendingWithTextPersister() {
        \ShortPixel\ShortPixel::log("RUN TEST: " . __FUNCTION__);
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => "text"));
        $sourceFolder = __DIR__ . "/data/big-pngs-pending";
        //$sourceFolder = __DIR__ . "/data/pngs-pending";
        $folderPath = self::$tempDir;
        try {
            \ShortPixel\recurseCopy($sourceFolder, $folderPath);

            $result = \ShortPixel\fromFolder($folderPath)->wait(1)->toFiles($folderPath);
            $this->assertEquals(2, count($result->pending));

            $result2 = \ShortPixel\fromFolder($folderPath)->wait(300)->toFiles($folderPath);

            //OriginalURL should be the same, as the second time it should send using the URL from the .shortpixel file
            //si aici comparam OriginalUrl cu cel de mai sus
            foreach($result->pending as $pend) {
                $match = 0;
                foreach($result2->succeeded as $suc) {
                    if($pend->OriginalURL === $suc->OriginalURL) {
                        $match++;
                    }
                }
                $this->assertEquals(1, $match);
            }

            $this->assertEquals(2, count($result2->succeeded));

        } finally {
            \ShortPixel\delTree($folderPath);
        }
    }

    public function testShouldSkipAlreadyProcessedFromFolderWithTextPersister()
    {
        \ShortPixel\ShortPixel::log("RUN TEST: " . __FUNCTION__);
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => "text"));
        $sourceFolder = __DIR__ . "/data/images-opt-txt";
        $folderPath = self::$tempDir;
        try {
            \ShortPixel\recurseCopy($sourceFolder, $folderPath);
            $cmd = \ShortPixel\fromFolder($folderPath);
            $files = $cmd->getData()["files"];
            $this->assertEquals(1, count($files));
            $this->assertEquals(substr($files[0], -12), "mistretz.jpg");
        } finally {
            \ShortPixel\delTree(self::$tempDir);
        }
    }

    public function testShouldSkipAllAlreadyProcessedFromFolderWithTextPersister()
    {
        \ShortPixel\ShortPixel::log("RUN TEST: " . __FUNCTION__);
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => "text"));
        $sourceFolder = __DIR__ . "/data/pngs-success";
        $folderPath = self::$tempDir;
        try {
            \ShortPixel\recurseCopy($sourceFolder, $folderPath);
            $cmd = \ShortPixel\fromFolder($folderPath);
            $files = $cmd->getData()["files"];
            $this->assertEquals(0, count($files));
        } finally {
            \ShortPixel\delTree(self::$tempDir);
        }
    }

    public function testIsOptimizedWithTextPersister()
    {
        \ShortPixel\ShortPixel::log("RUN TEST: " . __FUNCTION__);
        \ShortPixel\ShortPixel::setOptions(array("persist_type" => "text"));
        $optimizedFile = __DIR__ . "/data/images-opt-txt/cerbu.jpg";
        $this->assertTrue(\ShortPixel\isOptimized($optimizedFile));
    }

    /* EXIF Persister currently deactivated server side

        public function testShouldCompressPNGsFromFolderWithExifPersister() {

            $this->markTestSkipped('EXIF persister not available currently'); return;

            \ShortPixel\ShortPixel::setOptions(array("persist_type" => "exif"));
            $sourceFolder = __DIR__ . "/data/images1";
            $folderPath = self::$tempDir;
            $this->recurseCopy($sourceFolder, $folderPath);
            $result = \ShortPixel\fromFolder($folderPath)->wait(300)->toFiles($folderPath);

            if(count($result->succeeded) > 0) {

            } elseif(count($result->failed)) {
                $this->throwException("Failed");
            } elseif(count($result->same)) {
                $this->throwException("Optimized image is same size and shouldn't");
            } elseif(count($result->pending)) {
                echo("LossyFromURL - did not finish");
            }

            $this->delTree($folderPath);
        }

        public function testShouldCompressJPGsFromFolderWithExifPersister() {
            \ShortPixel\ShortPixel::setOptions(array("persist_type" => "exif"));
            $sourceFolder = __DIR__ . "/data/images-jpg";
            $folderPath = self::$tempDir;
            $this->recurseCopy($sourceFolder, $folderPath);
            $result = \ShortPixel\fromFolder($folderPath)->wait(300)->toFiles($folderPath);

            if(count($result->succeeded) > 0) {

            } elseif(count($result->failed)) {
                $this->throwException("Failed");
            } elseif(count($result->same)) {
                $this->throwException("Optimized image is same size and shouldn't");
            } elseif(count($result->pending)) {
                echo("LossyFromURL - did not finish");
            }

            $this->delTree($folderPath);
        }

        public function testShouldSkipAlreadyProcessedJPGsFromFolderWithExifPersister()
        {
            \ShortPixel\ShortPixel::setOptions(array("persist_type" => "exif"));
            $sourceFolder = __DIR__ . "/data/images-jpg-part";
            $folderPath = self::$tempDir;
            $this->recurseCopy($sourceFolder, $folderPath);
            $cmd = \ShortPixel\fromFolder($folderPath);
            $files = $cmd->getData()["files"];
            $this->assertEquals(count($files), 1);
            $this->assertEquals(substr($files[0], -22), "final referinta-07.jpg");
        }

        public function testShouldSkipAlreadyProcessedPMGsFromFolderWithExifPersister()
        {
            \ShortPixel\ShortPixel::setOptions(array("persist_type" => "exif"));
            $sourceFolder = __DIR__ . "/data/images1part";
            $folderPath = self::$tempDir;
            $this->recurseCopy($sourceFolder, $folderPath);
            $cmd = \ShortPixel\fromFolder($folderPath);
            $files = $cmd->getData()["files"];
            $this->assertEquals(count($files), 3);
            sort($files);
            $this->assertEquals(substr($files[0], -8), "1-12.png");
        }
    */
}
