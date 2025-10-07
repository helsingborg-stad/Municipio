<?php

namespace Municipio\SchemaData\ExternalContent\SourceReaders\FileSystem;

use PHPUnit\Framework\TestCase;

class FilesystemTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated()
    {
        $filesystem = new FileSystem();
        $this->assertInstanceOf(FileSystem::class, $filesystem);
    }

    #[TestDox('fileExists() returns true if file exists')]
    public function testFileExistsReturnsTrueIfFileExists()
    {
        $filesystem = new FileSystem();
        $this->assertTrue($filesystem->fileExists(__FILE__));
    }

    #[TestDox('fileExists() returns false if file does not exist')]
    public function testFileExistsReturnsFalseIfFileDoesNotExist()
    {
        $filesystem = new FileSystem();
        $this->assertFalse($filesystem->fileExists('non-existing-file.txt'));
    }

    #[TestDox('fileGetContents() returns file contents')]
    public function testFileGetContentsReturnsFileContents()
    {
        $filesystem = new FileSystem();
        $this->assertStringContainsString('namespace Municipio\SchemaData\ExternalContent\SourceReaders\FileSystem;', $filesystem->fileGetContents(__FILE__));
    }

    #[TestDox('fileGetContents() returns false if file does not exist')]
    public function testFileGetContentsReturnsFalseIfFileDoesNotExist()
    {
        $filesystem = new FileSystem();
        $this->assertFalse(@$filesystem->fileGetContents('non-existing-file.txt'));
    }
}
