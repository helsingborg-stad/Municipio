<?php

namespace Municipio;

use PHPUnit\Framework\TestCase;

class BootstrapTest extends TestCase
{
    /**
     * @testdox file can be loaded
     * @runInSeparateProcess
     */
    public function testFileCanBeLoaded()
    {
        // Required constants for including the Bootstrap file
        define('MUNICIPIO_PATH', '');
        define('ABSPATH', '');

        try {
            include_once __DIR__ . '/Bootstrap.php';
            $this->assertTrue(true, 'Bootstrap file loaded successfully.');
        } catch (\Throwable $e) {
            $this->fail('Bootstrap file could not be loaded: ' . $e->getMessage());
        }
    }
}
