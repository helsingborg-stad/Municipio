<?php

namespace Modularity\Tests;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class FilesTest extends TestCase {

    #[TestDox('The wp-content directory should not exist')]
    public function testWpContentDirectoryDoesNotExist() {
        // wp-content was accientally added, then removed. This test ensures it is not added again.
        $this->assertDirectoryDoesNotExist('wp-content');
    }
}